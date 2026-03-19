<?php

namespace Database\Seeders;

use App\Models\Surah;
use App\Models\Verse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class VerseSeeder extends Seeder
{
    /**
     * Seed the verses table from Quran Foundation API.
     */
    public function run(): void
    {
        $baseUrl = rtrim(env('QURAN_API_BASE_URL', 'https://api.quran.com/api/v4'), '/');
        $script = env('QURAN_API_SCRIPT', 'uthmani');
        $perPage = max(1, min((int) env('QURAN_API_PER_PAGE', 50), 50));
        $maxPages = (int) env('QURAN_API_MAX_PAGES', 0);
        $translationResourceId = (int) env('QURAN_API_TRANSLATION_RESOURCE_ID', 85);
        $translationPerPage = max(1, min((int) env('QURAN_API_TRANSLATION_PER_PAGE', 50), 50));
        $fetchTransliteration = filter_var(env('QURAN_API_FETCH_TRANSLITERATION', true), FILTER_VALIDATE_BOOL);

        $clientId = env('QURAN_API_CLIENT_ID');
        $authToken = env('QURAN_API_AUTH_TOKEN');

        if (empty($authToken)) {
            $authToken = env('QURAN_API_CLIENT_SECRET');
        }

        $publicHost = str_contains($baseUrl, 'api.quran.com/api/v4');
        $useAuthHeaders = !$publicHost && !empty($clientId) && !empty($authToken);

        if (!$useAuthHeaders) {
            $this->command?->warn('Quran API credentials missing/partial. Attempting unauthenticated request.');
        }

        $scriptField = 'text_' . $script;
        $page = 1;
        $totalPages = 1;
        $imported = 0;
        $revelationMap = Surah::query()
            ->pluck('revelation_type', 'surah_number')
            ->all();

        $this->command?->info("Importing Quran verses from API using script: {$script}");

        do {
            $request = Http::acceptJson()
                ->retry(3, 500, null, false)
                ->timeout(45);

            if ($useAuthHeaders) {
                $request = $request->withHeaders([
                    'x-client-id' => $clientId,
                    'x-auth-token' => $authToken,
                ]);
            }

            $response = $request->get("{$baseUrl}/quran/verses/{$script}", [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            if (!$response->successful()) {
                $status = $response->status();
                $this->command?->error("Failed fetching page {$page} (HTTP {$status}).");
                break;
            }

            $payload = $response->json();
            $apiVerses = data_get($payload, 'verses', []);

            if (!is_array($apiVerses) || empty($apiVerses)) {
                $this->command?->warn("No verses returned on page {$page}. Stopping import.");
                break;
            }

            $now = Carbon::now();
            $rows = [];

            foreach ($apiVerses as $item) {
                $surahNumber = (int) data_get($item, 'chapter_id');
                $verseNumber = (int) data_get($item, 'verse_number');

                if (($surahNumber < 1 || $verseNumber < 1) && !empty(data_get($item, 'verse_key'))) {
                    [$surahFromKey, $verseFromKey] = array_pad(explode(':', (string) data_get($item, 'verse_key'), 2), 2, null);
                    $surahNumber = (int) $surahFromKey;
                    $verseNumber = (int) $verseFromKey;
                }

                if ($surahNumber < 1 || $verseNumber < 1) {
                    continue;
                }

                $arabicText = (string) (data_get($item, $scriptField) ?: data_get($item, 'text_uthmani') ?: data_get($item, 'text_imlaei') ?: '');

                if ($arabicText === '') {
                    continue;
                }

                $rows[] = [
                    'surah_number' => $surahNumber,
                    'verse_number' => $verseNumber,
                    'arabic_text' => $arabicText,
                    'transliteration' => null,
                    'translation_english' => null,
                    'translation_bengali' => null,
                    'juz' => (int) data_get($item, 'juz_number') ?: null,
                    'page' => (int) data_get($item, 'page_number') ?: null,
                    'revelation_type' => $revelationMap[$surahNumber] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($rows)) {
                foreach (array_chunk($rows, 500) as $chunk) {
                    Verse::upsert(
                        $chunk,
                        ['surah_number', 'verse_number'],
                        [
                            'arabic_text',
                            'transliteration',
                            'translation_english',
                            'translation_bengali',
                            'juz',
                            'page',
                            'revelation_type',
                            'updated_at',
                        ]
                    );
                }

                $imported += count($rows);
            }

            $totalPages = (int) data_get($payload, 'pagination.total_pages', 1);
            $this->command?->info("Imported page {$page}/{$totalPages} (running total: {$imported})");

            $page++;
        } while ($page <= $totalPages && ($maxPages <= 0 || $page <= $maxPages));

        $this->command?->info("Verse import complete. Total processed verses: {$imported}");

        $translationUpdated = $this->importEnglishTranslations(
            $baseUrl,
            $translationResourceId,
            $translationPerPage,
            $useAuthHeaders,
            $clientId,
            $authToken
        );

        $this->command?->info("English translation sync complete. Total verses updated: {$translationUpdated}");

        if (!$fetchTransliteration) {
            $this->command?->warn('Skipping transliteration sync (QURAN_API_FETCH_TRANSLITERATION=false).');
            return;
        }

        $transliterationUpdated = $this->importVerseTransliterations();
        $this->command?->info("Transliteration sync complete. Total verses updated: {$transliterationUpdated}");
    }

    private function importVerseTransliterations(): int
    {
        $this->command?->info('Importing verse transliterations (word-by-word)...');

        $updated = 0;

        for ($chapter = 1; $chapter <= 114; $chapter++) {
            $page = 1;

            while (true) {
                try {
                    $response = Http::acceptJson()
                        ->retry(2, 300)
                        ->timeout(30)
                        ->get("https://api.quran.com/api/v4/verses/by_chapter/{$chapter}", [
                            'words' => 'true',
                            'word_fields' => 'transliteration',
                            'per_page' => 50,
                            'page' => $page,
                        ]);
                } catch (\Throwable $e) {
                    $this->command?->warn("Transliteration fetch failed for chapter {$chapter}, page {$page}: {$e->getMessage()}");
                    break;
                }

                if (!$response->successful()) {
                    $this->command?->warn("Transliteration request failed for chapter {$chapter}, page {$page} (HTTP {$response->status()}).");
                    break;
                }

                $payload = $response->json();
                $verses = data_get($payload, 'verses', []);

                if (!is_array($verses) || empty($verses)) {
                    break;
                }

                foreach ($verses as $verse) {
                    $verseKey = (string) data_get($verse, 'verse_key', '');

                    if ($verseKey === '' || !str_contains($verseKey, ':')) {
                        continue;
                    }

                    [$chapterPart, $versePart] = array_pad(explode(':', $verseKey, 2), 2, null);
                    $chapterId = (int) $chapterPart;
                    $verseNum = (int) $versePart;

                    if ($chapterId < 1 || $verseNum < 1) {
                        continue;
                    }

                    $words = data_get($verse, 'words', []);

                    if (!is_array($words) || empty($words)) {
                        continue;
                    }

                    $wordTransliterations = [];

                    foreach ($words as $word) {
                        $wordText = $this->extractWordTransliteration($word);

                        if ($wordText !== '') {
                            $wordTransliterations[] = $wordText;
                        }
                    }

                    $transliterationText = trim(preg_replace('/\s+/', ' ', implode(' ', $wordTransliterations)));

                    if ($transliterationText === '') {
                        continue;
                    }

                    $affected = Verse::where('surah_number', $chapterId)
                        ->where('verse_number', $verseNum)
                        ->update([
                            'transliteration' => $transliterationText,
                            'updated_at' => Carbon::now(),
                        ]);

                    $updated += $affected;
                }

                $nextPage = data_get($payload, 'pagination.next_page');

                if (empty($nextPage)) {
                    break;
                }

                $page = (int) $nextPage;
            }

            $this->command?->info("Transliterations synced for chapter {$chapter}/114");
        }

        return $updated;
    }

    private function extractWordTransliteration(array $word): string
    {
        $transliteration = data_get($word, 'transliteration');

        if (is_string($transliteration)) {
            return trim($transliteration);
        }

        if (is_array($transliteration)) {
            $text = data_get($transliteration, 'text');

            if (is_string($text)) {
                return trim($text);
            }

            foreach ($transliteration as $value) {
                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
        }

        return '';
    }

    private function importEnglishTranslations(
        string $baseUrl,
        int $resourceId,
        int $perPage,
        bool $useAuthHeaders,
        ?string $clientId,
        ?string $authToken
    ): int {
        $this->command?->info("Importing English translations (resource_id: {$resourceId})");

        $updated = 0;

        for ($chapter = 1; $chapter <= 114; $chapter++) {
            $chapterVerseNumbers = Verse::query()
                ->where('surah_number', $chapter)
                ->orderBy('verse_number')
                ->pluck('verse_number')
                ->values()
                ->all();

            $page = 1;
            $totalPages = 1;

            do {
                $request = Http::acceptJson()
                    ->retry(3, 500, null, false)
                    ->timeout(45);

                if ($useAuthHeaders) {
                    $request = $request->withHeaders([
                        'x-client-id' => $clientId,
                        'x-auth-token' => $authToken,
                    ]);
                }

                $response = $request->get("{$baseUrl}/translations/{$resourceId}/by_chapter/{$chapter}", [
                    'page' => $page,
                    'per_page' => $perPage,
                ]);

                if (!$response->successful()) {
                    $status = $response->status();
                    $this->command?->warn("Skipping chapter {$chapter}, translation page {$page} (HTTP {$status}).");
                    break;
                }

                $payload = $response->json();
                $translations = data_get($payload, 'translations', []);

                if (!is_array($translations) || empty($translations)) {
                    break;
                }

                $updates = [];

                foreach ($translations as $index => $item) {
                    $verseKey = (string) data_get($item, 'verse_key');

                    $surahNumber = $chapter;
                    $verseNumber = null;

                    if ($verseKey !== '' && str_contains($verseKey, ':')) {
                        [$surahPart, $versePart] = array_pad(explode(':', $verseKey, 2), 2, null);
                        $surahNumber = (int) $surahPart;
                        $verseNumber = (int) $versePart;
                    }

                    $globalIndex = (($page - 1) * $perPage) + $index;

                    if (($verseNumber === null || $verseNumber < 1) && isset($chapterVerseNumbers[$globalIndex])) {
                        $verseNumber = (int) $chapterVerseNumbers[$globalIndex];
                    }

                    if ($surahNumber < 1 || $verseNumber < 1) {
                        continue;
                    }

                    $text = (string) data_get($item, 'text', '');
                    $translationText = trim(strip_tags($text));

                    if ($translationText === '') {
                        continue;
                    }

                    $updates[] = [
                        'surah_number' => $surahNumber,
                        'verse_number' => $verseNumber,
                        'translation_english' => $translationText,
                        'updated_at' => Carbon::now(),
                    ];
                }

                if (!empty($updates)) {
                    foreach ($updates as $update) {
                        $affected = Verse::query()
                            ->where('surah_number', $update['surah_number'])
                            ->where('verse_number', $update['verse_number'])
                            ->update([
                                'translation_english' => $update['translation_english'],
                                'updated_at' => $update['updated_at'],
                            ]);

                        $updated += $affected;
                    }
                }

                $totalPages = (int) data_get($payload, 'pagination.total_pages', data_get($payload, 'pagination.total_pages=', 1));
                $page++;
            } while ($page <= $totalPages);

            $this->command?->info("Translations synced for chapter {$chapter}/114");
        }

        return $updated;
    }
}
