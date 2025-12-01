<?php

namespace Database\Seeders;

use App\Models\Surah;
use Illuminate\Database\Seeder;

class SurahSeeder extends Seeder
{
    /**
     * Seed the surahs table with sample surahs
     */
    public function run(): void
    {
        $surahs = [
            ['surah_number' => 1, 'name_arabic' => 'الفاتحة', 'name_english' => 'The Opening', 'name_transliteration' => 'Al-Fātiḥah', 'revelation_type' => 'Meccan', 'total_verses' => 7],
            ['surah_number' => 2, 'name_arabic' => 'البقرة', 'name_english' => 'Al-Baqarah', 'name_transliteration' => 'Al-Baqarah', 'revelation_type' => 'Medinan', 'total_verses' => 286],
            ['surah_number' => 3, 'name_arabic' => 'آل عمران', 'name_english' => 'Aal-E-Imran', 'name_transliteration' => 'Aal-E-Imran', 'revelation_type' => 'Medinan', 'total_verses' => 200],
            ['surah_number' => 4, 'name_arabic' => 'النساء', 'name_english' => 'An-Nisa', 'name_transliteration' => 'An-Nisa', 'revelation_type' => 'Medinan', 'total_verses' => 176],
            ['surah_number' => 5, 'name_arabic' => 'المائدة', 'name_english' => 'Al-Maidah', 'name_transliteration' => 'Al-Maidah', 'revelation_type' => 'Medinan', 'total_verses' => 120],
            ['surah_number' => 6, 'name_arabic' => 'الأنعام', 'name_english' => 'Al-Anaam', 'name_transliteration' => 'Al-Anaam', 'revelation_type' => 'Meccan', 'total_verses' => 165],
            ['surah_number' => 7, 'name_arabic' => 'الأعراف', 'name_english' => 'Al-Araf', 'name_transliteration' => 'Al-Araf', 'revelation_type' => 'Meccan', 'total_verses' => 206],
            ['surah_number' => 8, 'name_arabic' => 'الأنفال', 'name_english' => 'Al-Anfal', 'name_transliteration' => 'Al-Anfal', 'revelation_type' => 'Medinan', 'total_verses' => 75],
            ['surah_number' => 9, 'name_arabic' => 'التوبة', 'name_english' => 'At-Tawbah', 'name_transliteration' => 'At-Tawbah', 'revelation_type' => 'Medinan', 'total_verses' => 129],
            ['surah_number' => 10, 'name_arabic' => 'يونس', 'name_english' => 'Yunus', 'name_transliteration' => 'Yunus', 'revelation_type' => 'Meccan', 'total_verses' => 109],
        ];

        foreach ($surahs as $surah) {
            Surah::updateOrCreate(
                ['surah_number' => $surah['surah_number']], 
                $surah
            );
        }
    }
}