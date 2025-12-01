<?php

namespace Database\Seeders;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use Illuminate\Database\Seeder;

class BukhariChapter1Seeder extends Seeder
{
    public function run(): void
    {
        // Get Bukhari collection
        $bukhari = HadithCollection::where('name', 'Sahih Bukhari')->first();
        
        if (!$bukhari) {
            $this->command->error('Bukhari collection not found. Run TestHadithSeeder first.');
            return;
        }

        $csvFile = resource_path('views/hadiths/Bukhari/Chapter1.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error('Bukhari Chapter1.csv not found at: ' . $csvFile);
            return;
        }

        $this->command->info('Loading Bukhari Chapter 1...');
        
        $csvData = array_map('str_getcsv', file($csvFile));
        $header = array_shift($csvData); // Remove header
        
        $count = 0;
        $currentChapter = null;
        
        foreach ($csvData as $row) {
            if (count($row) < 16) continue;
            
            $data = array_combine($header, $row);
            
            // Clean data
            foreach ($data as &$value) {
                $value = trim($value);
                if ($value === 'nan' || $value === '') {
                    $value = null;
                }
            }
            
            // Process chapter
            if (!$currentChapter || $currentChapter->chapter_number != $data['Chapter_Number']) {
                $currentChapter = HadithChapter::updateOrCreate(
                    [
                        'collection_id' => $bukhari->id,
                        'chapter_number' => (float)$data['Chapter_Number']
                    ],
                    [
                        'chapter_name_english' => $data['Chapter_English'],
                        'chapter_name_arabic' => $data['Chapter_Arabic']
                    ]
                );
            }
            
            // Process hadith
            $hadithData = [
                'collection_id' => $bukhari->id,
                'chapter_id' => $currentChapter->id,
                'section_id' => null,
                'hadith_number' => (float)$data['Hadith_number'],
                'english_hadith' => $data['English_Hadith'],
                'english_isnad' => $data['English_Isnad'],
                'english_matn' => $data['English_Matn'],
                'arabic_hadith' => $data['Arabic_Hadith'],
                'arabic_isnad' => $data['Arabic_Isnad'],
                'arabic_matn' => $data['Arabic_Matn'],
                'arabic_comment' => $data['Arabic_Comment'],
                'english_grade' => $data['English_Grade'],
                'arabic_grade' => $data['Arabic_Grade']
            ];
            
            Hadith::updateOrCreate(
                [
                    'collection_id' => $bukhari->id,
                    'hadith_number' => (float)$data['Hadith_number']
                ],
                $hadithData
            );
            
            $count++;
            
            // Limit to first 10 hadiths for testing
            if ($count >= 10) break;
        }
        
        $this->command->info("Loaded {$count} hadiths from Bukhari Chapter 1");
    }
}