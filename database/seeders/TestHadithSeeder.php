<?php

namespace Database\Seeders;

use App\Models\Hadith;
use App\Models\HadithChapter;
use App\Models\HadithCollection;
use App\Models\HadithSection;
use Illuminate\Database\Seeder;

class TestHadithSeeder extends Seeder
{
    /**
     * Run a basic test seeding for development.
     */
    public function run(): void
    {
        // Create Bukhari collection
        $bukhari = HadithCollection::updateOrCreate(
            ['name' => 'Sahih Bukhari'],
            [
                'name_arabic' => 'صحيح البخاري',
                'description' => 'The most authentic hadith collection compiled by Imam Muhammad al-Bukhari.',
                'is_verified' => true,
                'accuracy_percentage' => null
            ]
        );

        // Create a test chapter
        $chapter = HadithChapter::updateOrCreate(
            [
                'collection_id' => $bukhari->id,
                'chapter_number' => 1.0
            ],
            [
                'chapter_name_english' => 'Revelation',
                'chapter_name_arabic' => 'كتاب بدء الوحى'
            ]
        );

        // Create a test section
        $section = HadithSection::updateOrCreate(
            [
                'chapter_id' => $chapter->id,
                'section_number' => 1.0
            ],
            [
                'section_name_english' => 'How the Divine Revelation started being revealed to Allah\'s Messenger',
                'section_name_arabic' => 'كَيْفَ كَانَ بَدْءُ الْوَحْىِ إِلَى رَسُولِ اللَّهِ صلى الله عليه وسلم'
            ]
        );

        // Create a test hadith
        Hadith::updateOrCreate(
            [
                'collection_id' => $bukhari->id,
                'hadith_number' => 1.0
            ],
            [
                'chapter_id' => $chapter->id,
                'section_id' => $section->id,
                'english_hadith' => 'Narrated \'Umar bin Al-Khattab: I heard Allah\'s Messenger (ﷺ) saying, "The reward of deeds depends upon the intentions and every person will get the reward according to what he has intended. So whoever emigrated for worldly benefits or for a woman to marry, his emigration was for what he emigrated for."',
                'english_isnad' => 'Narrated \'Umar bin Al-Khattab:',
                'english_matn' => 'I heard Allah\'s Messenger (ﷺ) saying, "The reward of deeds depends upon the intentions and every person will get the reward according to what he has intended. So whoever emigrated for worldly benefits or for a woman to marry, his emigration was for what he emigrated for."',
                'arabic_hadith' => 'حَدَّثَنَا الْحُمَيْدِيُّ عَبْدُاللَّهِ بْنُ الزُّبَيْرِ، قَالَ حَدَّثَنَا سُفْيَانُ، قَالَ حَدَّثَنَا يَحْيَى بْنُ سَعِيدٍ الأَنْصَارِيُّ، قَالَ أَخْبَرَنِي مُحَمَّدُ بْنُ إِبْرَاهِيمَ التَّيْمِيُّ، أَنَّهُ سَمِعَ عَلْقَمَةَ بْنَ وَقَّاصٍ اللَّيْثِيَّ، يَقُولُ سَمِعْتُ عُمَرَ بْنَ الْخَطَّابِ ـ رضى الله عنه ـ عَلَى الْمِنْبَرِ قَالَ سَمِعْتُ رَسُولَ اللَّهِ صلى الله عليه وسلم يَقُولُ ‏ ‏ إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ، وَإِنَّمَا لِكُلِّ امْرِئٍ مَا نَوَى، فَمَنْ كَانَتْ هِجْرَتُهُ إِلَى دُنْيَا يُصِيبُهَا أَوْ إِلَى امْرَأَةٍ يَنْكِحُهَا فَهِجْرَتُهُ إِلَى مَا هَاجَرَ إِلَيْهِ ‏',
                'arabic_isnad' => 'حَدَّثَنَا الْحُمَيْدِيُّ عَبْدُاللَّهِ بْنُ الزُّبَيْرِ، قَالَ حَدَّثَنَا سُفْيَانُ، قَالَ حَدَّثَنَا يَحْيَى بْنُ سَعِيدٍ الأَنْصَارِيُّ، قَالَ أَخْبَرَنِي مُحَمَّدُ بْنُ إِبْرَاهِيمَ التَّيْمِيُّ، أَنَّهُ سَمِعَ عَلْقَمَةَ بْنَ وَقَّاصٍ اللَّيْثِيَّ، يَقُولُ سَمِعْتُ عُمَرَ بْنَ الْخَطَّابِ ـ رضى الله عنه ـ عَلَى الْمِنْبَرِ قَالَ',
                'arabic_matn' => 'سَمِعْتُ رَسُولَ اللَّهِ صلى الله عليه وسلم يَقُولُ ‏ ‏ إِنَّمَا الأَعْمَالُ بِالنِّيَّاتِ، وَإِنَّمَا لِكُلِّ امْرِئٍ مَا نَوَى، فَمَنْ كَانَتْ هِجْرَتُهُ إِلَى دُنْيَا يُصِيبُهَا أَوْ إِلَى امْرَأَةٍ يَنْكِحُهَا فَهِجْرَتُهُ إِلَى مَا هَاجَرَ إِلَيْهِ ‏',
                'arabic_comment' => null,
                'english_grade' => 'Sahih-Authentic',
                'arabic_grade' => 'صحيح'
            ]
        );

        $this->command->info("Test hadith seeded successfully!");
    }
}