<?php

namespace Database\Seeders;

use App\Models\Surah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class SurahInfoSeeder extends Seeder
{
    /**
     * Seed surah descriptions from Quran chapter info API.
     */
    public function run(): void
    {
        $baseUrl = rtrim((string) env('QURAN_INFO_API_BASE_URL', 'https://api.quran.com/api/v4'), '/');

        for ($surahNumber = 1; $surahNumber <= 114; $surahNumber++) {
            try {
                $response = Http::timeout(10)
                    ->retry(2, 300)
                    ->get("{$baseUrl}/chapters/{$surahNumber}/info");

                if (!$response->successful()) {
                    $this->command?->warn("Failed to fetch surah info for {$surahNumber} (HTTP {$response->status()})");
                    usleep(300000);
                    continue;
                }

                $shortText = (string) data_get($response->json(), 'chapter_info.short_text', '');
                $shortText = trim(strip_tags($shortText));

                Surah::where('surah_number', $surahNumber)->update([
                    'description' => $shortText !== '' ? $shortText : null,
                ]);
            } catch (\Throwable $e) {
                $this->command?->warn("Failed to fetch surah info for {$surahNumber}: {$e->getMessage()}");
            }

            usleep(300000);
        }
    }
}
