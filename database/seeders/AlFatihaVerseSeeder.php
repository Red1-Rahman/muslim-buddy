<?php

namespace Database\Seeders;

use App\Models\Verse;
use Illuminate\Database\Seeder;

class AlFatihaVerseSeeder extends Seeder
{
    /**
     * Seed Al-Fatiha verses from JSON data
     */
    public function run(): void
    {
        $verses = [
            [
                'surah_number' => 1,
                'verse_number' => 1,
                'arabic_text' => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
                'translation_english' => 'In the name of Allah, the Most Gracious, the Most Merciful.',
                'transliteration' => 'Bismillāhir-Raḥmānir-Raḥīm',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 2,
                'arabic_text' => 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                'translation_english' => 'All praise is due to Allah, Lord of the worlds.',
                'transliteration' => 'Al-ḥamdu lillāhi rabbil-ʿālamīn',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 3,
                'arabic_text' => 'الرَّحْمَٰنِ الرَّحِيمِ',
                'translation_english' => 'The Most Gracious, the Most Merciful.',
                'transliteration' => 'Ar-Raḥmānir-Raḥīm',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 4,
                'arabic_text' => 'مَالِكِ يَوْمِ الدِّينِ',
                'translation_english' => 'Master of the Day of Judgment.',
                'transliteration' => 'Māliki yawmid-dīn',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 5,
                'arabic_text' => 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ',
                'translation_english' => 'You alone we worship, and You alone we ask for help.',
                'transliteration' => 'Iyyāka naʿbudu wa iyyāka nastaʿīn',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 6,
                'arabic_text' => 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ',
                'translation_english' => 'Guide us to the straight path,',
                'transliteration' => 'Ihdinaṣ-ṣirāṭal-mustaqīm',
            ],
            [
                'surah_number' => 1,
                'verse_number' => 7,
                'arabic_text' => 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ',
                'translation_english' => 'The path of those upon whom You have bestowed favor, not of those who have evoked [Your] anger or of those who are astray.',
                'transliteration' => 'Ṣirāṭal-ladhīna anʿamta ʿalayhim ghayril-maghḍūbi ʿalayhim wa laḍ-ḍāllīn',
            ],
        ];

        foreach ($verses as $verseData) {
            Verse::updateOrCreate(
                [
                    'surah_number' => $verseData['surah_number'],
                    'verse_number' => $verseData['verse_number']
                ],
                $verseData
            );
        }
    }
}