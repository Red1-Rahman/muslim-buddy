<?php

namespace Database\Seeders;

use App\Models\Verse;
use Illuminate\Database\Seeder;

class VerseSeeder extends Seeder
{
    /**
     * Seed the verses table with sample Quran verses
     * Note: This is a sample with Al-Fatiha. For complete Quran, you would need all 6,236 verses.
     */
    public function run(): void
    {
        // Al-Fatiha (Chapter 1) - Complete from JSON data
        $verses = [
            [
                'surah_number' => 1,
                'verse_number' => 1,
                'arabic_text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
                'translation_english' => 'In the name of Allah, the Most Gracious, the Most Merciful.',
                'transliteration' => 'Bismi Allahi alrrahmani alrraheemi',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 2,
                'arabic_text' => 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                'translation_english' => 'All praise is due to Allah, Lord of the worlds.',
                'transliteration' => 'Alhamdu lillahi rabbi alAAalameena',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 3,
                'arabic_text' => 'الرَّحْمَٰنِ الرَّحِيمِ',
                'translation_english' => 'The Most Gracious, the Most Merciful.',
                'transliteration' => 'Alrrahmani alrraheemi',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 4,
                'arabic_text' => 'مَالِكِ يَوْمِ الدِّينِ',
                'translation_english' => 'Master of the Day of Judgment.',
                'transliteration' => 'Maliki yawmi alddeeni',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 5,
                'arabic_text' => 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ',
                'translation_english' => 'You alone we worship, and You alone we ask for help.',
                'transliteration' => 'Iyyaka naAAbudu wa-iyyaka nastaAAeenu',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 6,
                'arabic_text' => 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ',
                'translation_english' => 'Guide us to the straight path,',
                'transliteration' => 'Ihdina alssirata almustaqeema',
                'juz' => 1,
                'page' => 1,
            ],
            [
                'surah_number' => 1,
                'verse_number' => 7,
                'arabic_text' => 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ',
                'translation_english' => 'The path of those upon whom You have bestowed favor, not of those who have evoked [Your] anger or of those who are astray.',
                'transliteration' => 'Sirata allatheena anAAamta AAalayhim ghayri almaghdoobi AAalayhim wala alddalleena',
                'juz' => 1,
                'page' => 1,
            ],
            // Sample verses from other chapters for demonstration
            [
                'surah_number' => 2,
                'verse_number' => 1,
                'arabic_text' => 'الٓمٓ',
                'translation_english' => 'Alif, Lam, Meem.',
                'transliteration' => 'Alif-lam-meem',
                'juz' => 1,
                'page' => 2,
            ],
            [
                'surah_number' => 2,
                'verse_number' => 2,
                'arabic_text' => 'ذَ ٰلِكَ ٱلۡكِتَـٰبُ لَا رَیۡبَ ۛ فِیهِ ۛ هُدࣰى لِّلۡمُتَّقِینَ',
                'translation_english' => 'This is the Book about which there is no doubt, a guidance for those conscious of Allah -',
                'transliteration' => 'Thalika alkitabu la rayba feehi hudan lilmuttaqeena',
                'juz' => 1,
                'page' => 2,
            ],
        ];

        foreach ($verses as $verse) {
            Verse::create($verse);
        }

        // You would need to add all 6,236 verses here for the complete Quran
        // This can be done by importing from a Quran API or database
        echo "Sample verses seeded. For complete Quran, please import all 6,236 verses.\n";
    }
}