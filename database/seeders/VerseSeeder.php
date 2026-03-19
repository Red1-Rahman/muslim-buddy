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
                ->retry(3, 500)
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
    }
}
