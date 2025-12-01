<?php

namespace Database\Seeders;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use App\Models\HadithSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HadithSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // First, create the collections
        $this->createCollections();
        
        // Then process each collection's CSV files
        $this->processCollectionFiles();
    }
    
    private function createCollections(): void
    {
        $collections = [
            [
                'name' => 'Sahih Bukhari',
                'name_arabic' => 'صحيح البخاري',
                'description' => 'The most authentic hadith collection compiled by Imam Muhammad al-Bukhari.',
                'is_verified' => true,
                'accuracy_percentage' => null,
                'folder' => 'Bukhari'
            ],
            [
                'name' => 'Sahih Muslim',
                'name_arabic' => 'صحيح مسلم',
                'description' => 'The second most authentic hadith collection compiled by Imam Muslim ibn al-Hajjaj.',
                'is_verified' => false,
                'accuracy_percentage' => 92.00,
                'folder' => 'Muslim'
            ],
            [
                'name' => 'Sunan Abu Dawud',
                'name_arabic' => 'سنن أبي داود',
                'description' => 'Hadith collection focused on legal rulings compiled by Abu Dawud.',
                'is_verified' => false,
                'accuracy_percentage' => 92.00,
                'folder' => 'AbuDaud'
            ],
            [
                'name' => 'Sunan Ibn Majah',
                'name_arabic' => 'سنن ابن ماجه',
                'description' => 'Hadith collection compiled by Ibn Majah, one of the six major hadith collections.',
                'is_verified' => false,
                'accuracy_percentage' => 92.00,
                'folder' => 'IbnMaja'
            ],
            [
                'name' => 'Sunan an-Nasa\'i',
                'name_arabic' => 'سنن النسائي',
                'description' => 'Hadith collection compiled by Imam an-Nasa\'i with focus on authentic narrations.',
                'is_verified' => false,
                'accuracy_percentage' => 92.00,
                'folder' => 'Nesai'
            ],
            [
                'name' => 'Jami\' at-Tirmidhi',
                'name_arabic' => 'جامع الترمذي',
                'description' => 'Hadith collection with detailed grading and commentary by Imam at-Tirmidhi.',
                'is_verified' => false,
                'accuracy_percentage' => 92.00,
                'folder' => 'Tirmizi'
            ],
        ];
        
        foreach ($collections as $collectionData) {
            $folder = $collectionData['folder'];
            unset($collectionData['folder']);
            
            $collection = HadithCollection::updateOrCreate(
                ['name' => $collectionData['name']],
                $collectionData
            );
            
            $this->command->info("Created collection: {$collection->name}");
        }
    }
    
    private function processCollectionFiles(): void
    {
        $hadithsPath = resource_path('views/hadiths');
        
        if (!File::exists($hadithsPath)) {
            $this->command->error("Hadiths directory not found at: {$hadithsPath}");
            return;
        }
        
        $collections = HadithCollection::all();
        
        foreach ($collections as $collection) {
            $this->processCollection($collection, $hadithsPath);
        }
    }
    
    private function processCollection(HadithCollection $collection, string $basePath): void
    {
        // Map collection names to folder names
        $folderMap = [
            'Sahih Bukhari' => 'Bukhari',
            'Sahih Muslim' => 'Muslim',
            'Sunan Abu Dawud' => 'AbuDaud',
            'Sunan Ibn Majah' => 'IbnMaja',
            'Sunan an-Nasa\'i' => 'Nesai',
            'Jami\' at-Tirmidhi' => 'Tirmizi',
        ];
        
        $folderName = $folderMap[$collection->name] ?? null;
        if (!$folderName) {
            $this->command->warn("No folder mapping found for collection: {$collection->name}");
            return;
        }
        
        $collectionPath = $basePath . DIRECTORY_SEPARATOR . $folderName;
        
        if (!File::exists($collectionPath)) {
            $this->command->warn("Collection folder not found: {$collectionPath}");
            return;
        }
        
        $this->command->info("Processing collection: {$collection->name}");
        
        // Get all CSV files in the collection directory
        $csvFiles = File::glob($collectionPath . DIRECTORY_SEPARATOR . '*.csv');
        
        // Sort files by chapter number
        usort($csvFiles, function($a, $b) {
            preg_match('/Chapter(\d+)\.csv/', basename($a), $matchA);
            preg_match('/Chapter(\d+)\.csv/', basename($b), $matchB);
            return (int)($matchA[1] ?? 0) <=> (int)($matchB[1] ?? 0);
        });
        
        foreach ($csvFiles as $csvFile) {
            $this->processCsvFile($collection, $csvFile);
        }
    }
    
    private function processCsvFile(HadithCollection $collection, string $csvFile): void
    {
        $this->command->info("Processing file: " . basename($csvFile));
        
        $csvData = array_map('str_getcsv', file($csvFile));
        $header = array_shift($csvData); // Remove header row
        
        $processedHadiths = 0;
        $currentChapter = null;
        $currentSection = null;
        
        foreach ($csvData as $row) {
            if (count($row) < 16) {
                continue; // Skip incomplete rows
            }
            
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
                        'collection_id' => $collection->id,
                        'chapter_number' => (float)$data['Chapter_Number']
                    ],
                    [
                        'chapter_name_english' => $data['Chapter_English'],
                        'chapter_name_arabic' => $data['Chapter_Arabic']
                    ]
                );
            }
            
            // Process section (if exists)
            $currentSection = null;
            if (!empty($data['Section_Number'])) {
                $currentSection = HadithSection::updateOrCreate(
                    [
                        'chapter_id' => $currentChapter->id,
                        'section_number' => (float)$data['Section_Number']
                    ],
                    [
                        'section_name_english' => $data['Section_English'],
                        'section_name_arabic' => $data['Section_Arabic']
                    ]
                );
            }
            
            // Process hadith
            $hadithData = [
                'collection_id' => $collection->id,
                'chapter_id' => $currentChapter->id,
                'section_id' => $currentSection ? $currentSection->id : null,
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
            
            $hadith = Hadith::updateOrCreate(
                [
                    'collection_id' => $collection->id,
                    'hadith_number' => (float)$data['Hadith_number']
                ],
                $hadithData
            );
            
            $processedHadiths++;
            
            // Show progress every 100 hadiths
            if ($processedHadiths % 100 == 0) {
                $this->command->info("Processed {$processedHadiths} hadiths from " . basename($csvFile));
            }
        }
        
        $this->command->info("Completed " . basename($csvFile) . " - {$processedHadiths} hadiths processed");
    }
}