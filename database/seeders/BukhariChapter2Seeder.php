<?php

namespace Database\Seeders;

use App\Models\HadithCollection;
use App\Models\HadithChapter;
use App\Models\HadithSection;
use App\Models\Hadith;
use Illuminate\Database\Seeder;

class BukhariChapter2Seeder extends Seeder
{
    /**
     * Seed Bukhari Chapter 2 - Belief (كتاب الإيمان) from CSV data
     */
    public function run(): void
    {
        // Get Bukhari collection
        $collection = HadithCollection::where('name', 'Sahih Bukhari')->first();
        if (!$collection) {
            $this->command->error('Bukhari collection not found. Please run the main hadith seeder first.');
            return;
        }

        // Create Chapter 2
        $chapter = HadithChapter::updateOrCreate(
            [
                'collection_id' => $collection->id,
                'chapter_number' => 2
            ],
            [
                'chapter_name_english' => 'Belief',
                'chapter_name_arabic' => 'كتاب الإيمان'
            ]
        );

        // Define sections and hadiths from CSV data
        $hadithsData = [
            // Hadith 8 - Section 2: Your invocation means your faith
            [
                'section_number' => 2,
                'section_english' => 'Your invocation means your faith',
                'section_arabic' => 'دُعَاؤُكُمْ إِيمَانُكُمْ',
                'hadith_number' => 8,
                'english_isnad' => 'Narrated Ibn \'Umar:',
                'english_matn' => 'Allah\'s Messenger (ﷺ) said: Islam is based on (the following) five(principles):1. To testify that none has the right to be worshipped but Allah andMuhammad is Allah\'s Messenger (ﷺ).2. To offer the (compulsory congregational) prayers dutifully andperfectly.3. To pay Zakat (i.e. obligatory charity) .4. To perform Hajj. (i.e. Pilgrimage to Mecca)5. To observe fast during the month of Ramadan.',
                'arabic_isnad' => 'حَدَّثَنَا عُبَيْدُ اللَّهِ بْنُ مُوسَى، قَالَ أَخْبَرَنَا حَنْظَلَةُ بْنُ أَبِي سُفْيَانَ، عَنْ عِكْرِمَةَ بْنِ خَالِدٍ، عَنِ ابْنِ عُمَرَ ـ رضى الله عنهما ـ قَالَ',
                'arabic_matn' => 'قَالَ رَسُولُ اللَّهِ صلى الله عليه وسلم ‏ ‏ بُنِيَ الإِسْلاَمُ عَلَى خَمْسٍ شَهَادَةِ أَنْ لاَ إِلَهَ إِلاَّ اللَّهُ وَأَنَّ مُحَمَّدًا رَسُولُ اللَّهِ، وَإِقَامِ الصَّلاَةِ، وَإِيتَاءِ الزَّكَاةِ، وَالْحَجِّ، وَصَوْمِ رَمَضَانَ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 9 - Section 3: (What is said) regarding the deeds of faith
            [
                'section_number' => 3,
                'section_english' => '(What is said) regarding the deeds of faith',
                'section_arabic' => 'أُمُورِ الإِيمَانِ',
                'hadith_number' => 9,
                'english_isnad' => 'Narrated Abu Huraira:',
                'english_matn' => 'The Prophet (ﷺ) said, "Faith (Belief) consists of more than sixty branches(i.e. parts). And Haya (This term "Haya" covers a large number ofconcepts which are to be taken together; amongst them are selfrespect, modesty, bashfulness, and scruple, etc.) is a part offaith."',
                'arabic_isnad' => 'حَدَّثَنَا عَبْدُ اللَّهِ بْنُ مُحَمَّدٍ، قَالَ حَدَّثَنَا أَبُو عَامِرٍ الْعَقَدِيُّ، قَالَ حَدَّثَنَا سُلَيْمَانُ بْنُ بِلاَلٍ، عَنْ عَبْدِ اللَّهِ بْنِ دِينَارٍ، عَنْ أَبِي صَالِحٍ، عَنْ أَبِي هُرَيْرَةَ ـ رضى الله عنه ـ عَنِ',
                'arabic_matn' => 'النَّبِيِّ صلى الله عليه وسلم قَالَ ‏ ‏ الإِيمَانُ بِضْعٌ وَسِتُّونَ شُعْبَةً، وَالْحَيَاءُ شُعْبَةٌ مِنَ الإِيمَانِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 10 - Section 4: A Muslim is the one who avoids harming Muslims
            [
                'section_number' => 4,
                'section_english' => 'A Muslim is the one who avoids harming Muslims with his tongue and hands',
                'section_arabic' => 'الْمُسْلِمُ مَنْ سَلِمَ الْمُسْلِمُونَ مِنْ لِسَانِهِ وَيَدِهِ',
                'hadith_number' => 10,
                'english_isnad' => 'Narrated \'Abdullah bin \'Amr:',
                'english_matn' => 'The Prophet (ﷺ) said, "A Muslim is the one who avoids harming Muslims withhis tongue and hands. And a Muhajir (emigrant) is the one who gives up(abandons) all what Allah has forbidden."',
                'arabic_isnad' => 'حَدَّثَنَا آدَمُ بْنُ أَبِي إِيَاسٍ، قَالَ حَدَّثَنَا شُعْبَةُ، عَنْ عَبْدِ اللَّهِ بْنِ أَبِي السَّفَرِ، وَإِسْمَاعِيلَ، عَنِ الشَّعْبِيِّ، عَنْ عَبْدِ اللَّهِ بْنِ عَمْرٍو ـ رضى الله عنهما ـ عَنِ',
                'arabic_matn' => 'النَّبِيِّ صلى الله عليه وسلم قَالَ ‏ ‏ الْمُسْلِمُ مَنْ سَلِمَ الْمُسْلِمُونَ مِنْ لِسَانِهِ وَيَدِهِ، وَالْمُهَاجِرُ مَنْ هَجَرَ مَا نَهَى اللَّهُ عَنْهُ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 11 - Section 5: Whose Islam is the best
            [
                'section_number' => 5,
                'section_english' => 'Whose Islam is the best (Who is the best Muslim)?',
                'section_arabic' => 'أَىُّ الإِسْلاَمِ أَفْضَلُ',
                'hadith_number' => 11,
                'english_isnad' => 'Narrated Abu Musa:',
                'english_matn' => 'Some people asked Allah\'s Messenger (ﷺ), "Whose Islam is the best? i.e. (Whois a very good Muslim)?" He replied, "One who avoids harming theMuslims with his tongue and hands."',
                'arabic_isnad' => 'حَدَّثَنَا سَعِيدُ بْنُ يَحْيَى بْنِ سَعِيدٍ الْقُرَشِيِّ، قَالَ حَدَّثَنَا أَبِي قَالَ، حَدَّثَنَا أَبُو بُرْدَةَ بْنُ عَبْدِ اللَّهِ بْنِ أَبِي بُرْدَةَ، عَنْ أَبِي بُرْدَةَ، عَنْ أَبِي مُوسَى ـ رضى الله عنه ـ قَالَ',
                'arabic_matn' => 'قَالُوا يَا رَسُولَ اللَّهِ أَىُّ الإِسْلاَمِ أَفْضَلُ قَالَ ‏ ‏ مَنْ سَلِمَ الْمُسْلِمُونَ مِنْ لِسَانِهِ وَيَدِهِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 12 - Section 6: To feed (others) is a part of Islam
            [
                'section_number' => 6,
                'section_english' => 'To feed (others) is a part of Islam',
                'section_arabic' => 'إِطْعَامُ الطَّعَامِ مِنَ الإِسْلاَمِ',
                'hadith_number' => 12,
                'english_isnad' => 'Narrated \'Abdullah bin \'Amr:',
                'english_matn' => 'A man asked the Prophet (ﷺ) , "What sort of deeds or (what qualities of)Islam are good?" The Prophet (ﷺ) replied, \'To feed (the poor) and greetthose whom you know and those whom you do not Know\'',
                'arabic_isnad' => 'حَدَّثَنَا عَمْرُو بْنُ خَالِدٍ، قَالَ حَدَّثَنَا اللَّيْثُ، عَنْ يَزِيدَ، عَنْ أَبِي الْخَيْرِ، عَنْ عَبْدِ اللَّهِ بْنِ عَمْرٍو ـ رضى الله عنهما ـ',
                'arabic_matn' => 'أَنَّ رَجُلاً، سَأَلَ النَّبِيَّ صلى الله عليه وسلم أَىُّ الإِسْلاَمِ خَيْرٌ قَالَ ‏ ‏ تُطْعِمُ الطَّعَامَ، وَتَقْرَأُ السَّلاَمَ عَلَى مَنْ عَرَفْتَ وَمَنْ لَمْ تَعْرِفْ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 13 - Section 7: To like for one's brother what one likes for himself
            [
                'section_number' => 7,
                'section_english' => 'To like for one\'s (Muslim\'s) brother what one likes for himself is a part of faith',
                'section_arabic' => 'مِنَ الإِيمَانِ أَنْ يُحِبَّ لأَخِيهِ مَا يُحِبُّ لِنَفْسِهِ',
                'hadith_number' => 13,
                'english_isnad' => 'Narrated Anas:',
                'english_matn' => 'The Prophet (ﷺ) said, "None of you will have faith till he wishes for his(Muslim) brother what he likes for himself."',
                'arabic_isnad' => 'حَدَّثَنَا مُسَدَّدٌ، قَالَ حَدَّثَنَا يَحْيَى، عَنْ شُعْبَةَ، عَنْ قَتَادَةَ، عَنْ أَنَسٍ ـ رضى الله عنه ـ عَنِ النَّبِيِّ صلى الله عليه وسلم‏.‏ وَعَنْ حُسَيْنٍ الْمُعَلِّمِ، قَالَ حَدَّثَنَا قَتَادَةُ، عَنْ أَنَسٍ،',
                'arabic_matn' => 'عَنِ النَّبِيِّ صلى الله عليه وسلم قَالَ ‏ ‏ لا يُؤْمِنُ أَحَدُكُمْ حَتَّى يُحِبَّ لأَخِيهِ مَا يُحِبُّ لِنَفْسِهِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 14 - Section 8: To love the Messenger is a part of faith
            [
                'section_number' => 8,
                'section_english' => 'To love the Messenger (Muhammad saws) is a part of faith',
                'section_arabic' => 'حُبُّ الرَّسُولِ صلى الله عليه وسلم مِنَ الإِيمَانِ',
                'hadith_number' => 14,
                'english_isnad' => 'Narrated Abu Huraira:',
                'english_matn' => '"Allah\'s Messenger (ﷺ) said, "By Him in Whose Hands my life is, none of youwill have faith till he loves me more than his father and hischildren."',
                'arabic_isnad' => 'حَدَّثَنَا أَبُو الْيَمَانِ، قَالَ أَخْبَرَنَا شُعَيْبٌ، قَالَ حَدَّثَنَا أَبُو الزِّنَادِ، عَنِ الأَعْرَجِ، عَنْ أَبِي هُرَيْرَةَ ـ رضى الله عنه ـ',
                'arabic_matn' => 'أَنَّ رَسُولَ اللَّهِ صلى الله عليه وسلم قَالَ ‏ ‏ فَوَالَّذِي نَفْسِي بِيَدِهِ لاَ يُؤْمِنُ أَحَدُكُمْ حَتَّى أَكُونَ أَحَبَّ إِلَيْهِ مِنْ وَالِدِهِ وَوَلَدِهِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 15 - Section 8: To love the Messenger (continued)
            [
                'section_number' => 8,
                'section_english' => 'To love the Messenger (Muhammad saws) is a part of faith',
                'section_arabic' => 'حُبُّ الرَّسُولِ صلى الله عليه وسلم مِنَ الإِيمَانِ',
                'hadith_number' => 15,
                'english_isnad' => 'Narrated Anas:',
                'english_matn' => 'The Prophet (ﷺ) said "None of you will have faith till he loves me morethan his father, his children and all mankind."',
                'arabic_isnad' => 'حَدَّثَنَا يَعْقُوبُ بْنُ إِبْرَاهِيمَ، قَالَ حَدَّثَنَا ابْنُ عُلَيَّةَ، عَنْ عَبْدِ الْعَزِيزِ بْنِ صُهَيْبٍ، عَنْ أَنَسٍ، عَنِ النَّبِيِّ صلى الله عليه وسلم ح وَحَدَّثَنَا آدَمُ، قَالَ حَدَّثَنَا شُعْبَةُ، عَنْ قَتَادَةَ، عَنْ أَنَسٍ، قَالَ',
                'arabic_matn' => 'قَالَ النَّبِيُّ صلى الله عليه وسلم ‏ ‏ لاَ يُؤْمِنُ أَحَدُكُمْ حَتَّى أَكُونَ أَحَبَّ إِلَيْهِ مِنْ وَالِدِهِ وَوَلَدِهِ وَالنَّاسِ أَجْمَعِينَ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 16 - Section 9: Sweetness of faith
            [
                'section_number' => 9,
                'section_english' => 'Sweetness (delight) of faith',
                'section_arabic' => 'حَلاَوَةِ الإِيمَانِ',
                'hadith_number' => 16,
                'english_isnad' => 'Narrated Anas:',
                'english_matn' => 'The Prophet (ﷺ) said, "Whoever possesses the following three qualitieswill have the sweetness (delight) of faith:1. The one to whom Allah and His Apostle becomes dearer than anythingelse.2. Who loves a person and he loves him only for Allah\'s sake.3. Who hates to revert to Atheism (disbelief) as he hates to be throwninto the fire."',
                'arabic_isnad' => 'حَدَّثَنَا مُحَمَّدُ بْنُ الْمُثَنَّى، قَالَ حَدَّثَنَا عَبْدُ الْوَهَّابِ الثَّقَفِيُّ، قَالَ حَدَّثَنَا أَيُّوبُ، عَنْ أَبِي قِلاَبَةَ، عَنْ أَنَسٍ، عَنِ',
                'arabic_matn' => 'النَّبِيِّ صلى الله عليه وسلم قَالَ ‏ ‏ ثَلاَثٌ مَنْ كُنَّ فِيهِ وَجَدَ حَلاَوَةَ الإِيمَانِ أَنْ يَكُونَ اللَّهُ وَرَسُولُهُ أَحَبَّ إِلَيْهِ مِمَّا سِوَاهُمَا، وَأَنْ يُحِبَّ الْمَرْءَ لاَ يُحِبُّهُ إِلاَّ لِلَّهِ، وَأَنْ يَكْرَهَ أَنْ يَعُودَ فِي الْكُفْرِ كَمَا يَكْرَهُ أَنْ يُقْذَفَ فِي النَّارِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 17 - Section 10: To love the Ansar is a sign of faith
            [
                'section_number' => 10,
                'section_english' => 'To love the Ansar is a sign of faith',
                'section_arabic' => 'عَلاَمَةُ الإِيمَانِ حُبُّ الأَنْصَارِ',
                'hadith_number' => 17,
                'english_isnad' => 'Narrated Anas:',
                'english_matn' => 'The Prophet (ﷺ) said, "Love for the Ansar is a sign of faith and hatredfor the Ansar is a sign of hypocrisy."',
                'arabic_isnad' => 'حَدَّثَنَا أَبُو الْوَلِيدِ، قَالَ حَدَّثَنَا شُعْبَةُ، قَالَ أَخْبَرَنِي عَبْدُ اللَّهِ بْنُ عَبْدِ اللَّهِ بْنِ جَبْرٍ، قَالَ سَمِعْتُ أَنَسًا، عَنِ',
                'arabic_matn' => 'النَّبِيِّ صلى الله عليه وسلم قَالَ ‏ ‏ آيَةُ الإِيمَانِ حُبُّ الأَنْصَارِ، وَآيَةُ النِّفَاقِ بُغْضُ الأَنْصَارِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 18 - Section 11: The pledge
            [
                'section_number' => 11,
                'section_english' => 'The pledge of allegiance',
                'section_arabic' => 'البيعة',
                'hadith_number' => 18,
                'english_isnad' => 'Narrated \'Ubada bin As-Samit:',
                'english_matn' => 'who took part in the battle of Badr and was a Naqib (a person headinga group of six persons), on the night of Al-\'Aqaba pledge: Allah\'sApostle said while a group of his companions were around him, "Swearallegiance to me for:1. Not to join anything in worship along with Allah.2. Not to steal.3. Not to commit illegal sexual intercourse.4. Not to kill your children.5. Not to accuse an innocent person (to spread such an accusationamong people).6. Not to be disobedient (when ordered) to do good deed."The Prophet (ﷺ) added: "Whoever among you fulfills his pledge will berewarded by Allah. And whoever indulges in any one of them (except theascription of partners to Allah) and gets the punishment in thisworld, that punishment will be an expiation for that sin. And if oneindulges in any of them, and Allah conceals his sin, it is up to Himto forgive or punish him (in the Hereafter)." \'Ubada bin As-Samitadded: "So we swore allegiance for these." (points to Allah\'sApostle)',
                'arabic_isnad' => 'حَدَّثَنَا أَبُو الْيَمَانِ، قَالَ أَخْبَرَنَا شُعَيْبٌ، عَنِ الزُّهْرِيِّ، قَالَ أَخْبَرَنِي أَبُو إِدْرِيسَ، عَائِذُ اللَّهِ بْنُ عَبْدِ اللَّهِ أَنَّ عُبَادَةَ بْنَ الصَّامِتِ ـ رضى الله عنه ـ',
                'arabic_matn' => 'وَكَانَ شَهِدَ بَدْرًا، وَهُوَ أَحَدُ النُّقَبَاءِ لَيْلَةَ الْعَقَبَةِ ـ أَنَّ رَسُولَ اللَّهِ صلى الله عليه وسلم قَالَ وَحَوْلَهُ عِصَابَةٌ مِنْ أَصْحَابِهِ ‏ ‏ بَايِعُونِي عَلَى أَنْ لاَ تُشْرِكُوا بِاللَّهِ شَيْئًا، وَلاَ تَسْرِقُوا، وَلاَ تَزْنُوا، وَلاَ تَقْتُلُوا أَوْلاَدَكُمْ، وَلاَ تَأْتُوا بِبُهْتَانٍ تَفْتَرُونَهُ بَيْنَ أَيْدِيكُمْ وَأَرْجُلِكُمْ، وَلاَ تَعْصُوا فِي مَعْرُوفٍ، فَمَنْ وَفَى مِنْكُمْ فَأَجْرُهُ عَلَى اللَّهِ، وَمَنْ أَصَابَ مِنْ ذَلِكَ شَيْئًا فَعُوقِبَ فِي الدُّنْيَا فَهُوَ كَفَّارَةٌ لَهُ، وَمَنْ أَصَابَ مِنْ ذَلِكَ شَيْئًا ثُمَّ سَتَرَهُ اللَّهُ، فَهُوَ إِلَى اللَّهِ إِنْ شَاءَ عَفَا عَنْهُ، وَإِنْ شَاءَ عَاقَبَهُ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 19 - Section 12: To flee from Al-Fitn is part of religion
            [
                'section_number' => 12,
                'section_english' => ' To flee (run away) from Al-Fitn (afflictions and trials), is a part of religion',
                'section_arabic' => 'مِنَ الدِّينِ الْفِرَارُ مِنَ الْفِتَنِ',
                'hadith_number' => 19,
                'english_isnad' => 'Narrated Abu Said Al-Khudri:',
                'english_matn' => 'Allah\'s Messenger (ﷺ) said, "A time will soon come when the best property of a Muslim will be sheep which he will take on the top of mountains and the places of rainfall (valleys) so as to flee with his religion from afflictions."',
                'arabic_isnad' => 'حَدَّثَنَا عَبْدُ اللَّهِ بْنُ مَسْلَمَةَ، عَنْ مَالِكٍ، عَنْ عَبْدِ الرَّحْمَنِ بْنِ عَبْدِ اللَّهِ بْنِ عَبْدِ الرَّحْمَنِ بْنِ أَبِي صَعْصَعَةَ، عَنْ أَبِيهِ، عَنْ أَبِي سَعِيدٍ الْخُدْرِيِّ، أَنَّهُ قَالَ',
                'arabic_matn' => 'قَالَ رَسُولُ اللَّهِ صلى الله عليه وسلم ‏ ‏ يُوشِكُ أَنْ يَكُونَ خَيْرَ مَالِ الْمُسْلِمِ غَنَمٌ يَتْبَعُ بِهَا شَعَفَ الْجِبَالِ وَمَوَاقِعَ الْقَطْرِ، يَفِرُّ بِدِينِهِ مِنَ الْفِتَنِ ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ],
            // Hadith 20 - Section 13: The statement of the Prophet
            [
                'section_number' => 13,
                'section_english' => ' The statement of the Prophet (saws): "I know Allah Ta\'ala better, than all of you do."',
                'section_arabic' => 'قَوْلِ النَّبِيِّ صلى الله عليه وسلم ‏«أَنَا أَعْلَمُكُمْ بِاللَّهِ»',
                'hadith_number' => 20,
                'english_isnad' => 'Narrated \'Aisha:',
                'english_matn' => 'Whenever Allah\'s Messenger (ﷺ) ordered the Muslims to do something, he usedto order them deeds which were easy for them to do, (according totheir strength and endurance). They said, "O Allah\'s Messenger (ﷺ)! We are notlike you. Allahhas forgiven your past and future sins." So Allah\'sApostle became angry and it was apparent on his face. He said, "I amthe most Allah fearing, and know Allah better than all of you do."',
                'arabic_isnad' => 'حَدَّثَنَا مُحَمَّدُ بْنُ سَلاَمٍ، قَالَ أَخْبَرَنَا عَبْدَةُ، عَنْ هِشَامٍ، عَنْ أَبِيهِ، عَنْ عَائِشَةَ، قَالَتْ',
                'arabic_matn' => 'كَانَ رَسُولُ اللَّهِ صلى الله عليه وسلم إِذَا أَمَرَهُمْ أَمَرَهُمْ مِنَ الأَعْمَالِ بِمَا يُطِيقُونَ قَالُوا إِنَّا لَسْنَا كَهَيْئَتِكَ يَا رَسُولَ اللَّهِ، إِنَّ اللَّهَ قَدْ غَفَرَ لَكَ مَا تَقَدَّمَ مِنْ ذَنْبِكَ وَمَا تَأَخَّرَ‏.‏ فَيَغْضَبُ حَتَّى يُعْرَفَ الْغَضَبُ فِي وَجْهِهِ ثُمَّ يَقُولُ ‏ ‏ إِنَّ أَتْقَاكُمْ وَأَعْلَمَكُمْ بِاللَّهِ أَنَا ‏',
                'grade_english' => 'Sahih-Authentic',
                'grade_arabic' => 'صحيح'
            ]
        ];

        // Process each hadith
        foreach ($hadithsData as $hadithData) {
            // Create or get section
            $section = HadithSection::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'section_number' => $hadithData['section_number']
                ],
                [
                    'section_name_english' => $hadithData['section_english'],
                    'section_name_arabic' => $hadithData['section_arabic']
                ]
            );

            // Create hadith
            Hadith::updateOrCreate(
                [
                    'collection_id' => $collection->id,
                    'chapter_id' => $chapter->id,
                    'hadith_number' => $hadithData['hadith_number']
                ],
                [
                    'section_id' => $section->id,
                    'english_hadith' => $hadithData['english_isnad'] . ' ' . $hadithData['english_matn'],
                    'english_isnad' => $hadithData['english_isnad'],
                    'english_matn' => $hadithData['english_matn'],
                    'arabic_hadith' => $hadithData['arabic_isnad'] . ' ' . $hadithData['arabic_matn'],
                    'arabic_isnad' => $hadithData['arabic_isnad'],
                    'arabic_matn' => $hadithData['arabic_matn'],
                    'english_grade' => $hadithData['grade_english'],
                    'arabic_grade' => $hadithData['grade_arabic']
                ]
            );
        }

        $this->command->info('Successfully seeded Bukhari Chapter 2 - Belief with 13 hadiths');
    }
}