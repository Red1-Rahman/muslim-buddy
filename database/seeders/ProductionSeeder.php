<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Only seed if surahs or verses table is empty — prevents re-seeding on redeploy
        if (\App\Models\Surah::count() === 0 || \App\Models\Verse::count() === 0) {
            $this->call([
                SurahSeeder::class,
                VerseSeeder::class,
                SurahInfoSeeder::class,
            ]);
        } else {
            $this->command?->info('Static content already seeded. Skipping.');
        }

        // TestHadithSeeder is intentionally excluded from production —
        // it contains sample/test data only.
    }
}
