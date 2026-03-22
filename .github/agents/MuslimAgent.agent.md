---
name: MuslimAgent
description: Specialist agent for Muslim Buddy app development, deployment, and Turso/libSQL driver bug workarounds.
argument-hint: A development task, bug fix, query optimization, or deployment issue specific to Muslim Buddy.
---

## Stack
- Laravel 10, PHP 8.2, Blade + Tailwind CSS
- Database: SQLite (local) / Turso libSQL (production)
- Deployment: Render (Docker), repo: github.com/Red1-Rahman/muslim-buddy
- Live URL: https://muslim-buddy.onrender.com

## Local Development
- Always use `./start.sh` to run locally — never `php artisan serve` directly
- The shell exports `DB_CONNECTION=mysql` which breaks local Turso queries
- Local DB is SQLite, production is Turso

## Turso / libSQL Driver Bug (v1.0.14)
**Critical:** The Turso Laravel driver drops all positional bindings except the first.
This means any query with 2+ bound parameters will silently lose all but the first.

### Rules to follow in ALL Laravel queries:
1. Never use `->where('column', $value)` as the last clause before `->count()` or another clause — inline it with `whereRaw`
2. Never use `->whereBetween('col', [$a, $b])` — replace with `->whereRaw('"col" BETWEEN \'$a\' AND \'$b\'')`
3. Never use `->where('boolean_col', true/false)` — use `->whereRaw('"col" = 1')` or `->whereRaw('"col" = 0')`
4. Boolean/integer columns affected: `is_read`, `is_completed`, `is_memorized`, `is_understood`, `on_time`, `in_congregation`, `at_mosque`
5. When chaining multiple `->where()` calls, only the FIRST bound value is safe — all others must be inlined via `whereRaw`

### Safe pattern:
```php
// SAFE — only one bound param (:p0), rest inlined
Model::where('user_id', $userId)
    ->whereRaw('"prayer_date" = \'' . $date . '\'')
    ->whereRaw('"is_completed" = 1')
    ->count();

// UNSAFE — driver drops :p1 and :p2
Model::where('user_id', $userId)
    ->where('prayer_date', $date)
    ->where('is_completed', true)
    ->count();
```

### whereHas() caveat:
`whereHas()` with closures that use `->where()` inside are also susceptible — subquery bindings can get dropped. Always use `whereRaw` inside `whereHas` closures:
```php
// UNSAFE — subquery bindings dropped
Model::whereHas('relation', function ($q) {
    $q->where('column', $value);
});

// SAFE — inline all subquery params
Model::whereHas('relation', function ($q) use ($value) {
    $q->whereRaw('"column" = \'' . $value . '\'');
});
```

## Docker / Deployment
- Vendor patches live in `docker/patch-libsql.php` — always run `php -l docker/patch-libsql.php` before committing
- Patch fixes URL bug in `LibSQLDatabase.php` via sed in Dockerfile
- To test patches locally against a clean vendor: `rm -rf vendor/tursodatabase && composer install`
- Blade view cache can serve stale views — run `php artisan view:clear` if views look wrong

### Patch Details (docker/patch-libsql.php)
Three patches are applied at container build time:

1. **LibSQLConnection::run() override** — Converts all positional bindings to named bindings (`:p0`, `:p1`, etc.) before calling parent, ensuring at least one binding survives the driver bug. This is the foundational fix that keeps the first param safe.

2. **LibSQLPDOStatement bindPositional fix** — When `execute()` receives positional params, they're converted to named params (without leading colon in keys: `p0`, `p1`, etc.) before calling `bindNamed()`, matching what the raw LibSQL statement expects.

3. **LibSQLConnection select() direct query fix** — Normalizes positional `?` placeholders to `:pN` in the SQL, converts bindings to named format, then calls `query($bindings)` directly instead of through the broken binding flow. This bypasses the problematic `execute()` path entirely.

**Important:** `config:cache` is run at container startup in `docker/entrypoint.sh`, so any `.env` changes require a full redeploy (not just a container restart) to take effect.

## Render Environment Variables
APP_KEY, APP_NAME=Muslim Buddy, APP_ENV=production, APP_DEBUG=false
DB_CONNECTION=libsql
TURSO_DB_URL=libsql://muslim-buddy-red1-rahman.aws-ap-northeast-1.turso.io
TURSO_DB_TOKEN=<rotate with: turso db tokens create muslim-buddy --expiration none>
SESSION_DRIVER=file, CACHE_DRIVER=file, LOG_CHANNEL=stderr, QUEUE_CONNECTION=sync
GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI
QURAN_RECITATION_ID=7, QURAN_API_BASE_URL, QURAN_API_CLIENT_ID, QURAN_API_CLIENT_SECRET

## Turso Database Schema
```
CREATE TABLE `migrations` (
    `id` integer PRIMARY KEY,
    `migration` text NOT NULL,
    `batch` integer NOT NULL
);
CREATE TABLE `personal_access_tokens` (
    `id` integer PRIMARY KEY,
    `tokenable_type` text NOT NULL,
    `tokenable_id` integer NOT NULL,
    `name` text NOT NULL,
    `token` text NOT NULL,
    `abilities` text,
    `last_used_at` numeric,
    `expires_at` numeric,
    `created_at` numeric,
    `updated_at` numeric
);
CREATE TABLE `users` (
    `id` integer PRIMARY KEY,
    `name` text NOT NULL,
    `email` text NOT NULL,
    `email_verified_at` numeric,
    `password` text NOT NULL,
    `remember_token` text,
    `latitude` numeric,
    `longitude` numeric,
    `location_name` text,
    `calculation_method` text DEFAULT 'MuslimWorldLeague' NOT NULL,
    `madhab` text DEFAULT 'Shafi' NOT NULL,
    `timezone` text DEFAULT 'UTC' NOT NULL,
    `bio` text,
    `avatar` text,
    `total_points` integer DEFAULT 0 NOT NULL,
    `prayer_streak` integer DEFAULT 0 NOT NULL,
    `last_prayer_date` numeric,
    `created_at` numeric,
    `updated_at` numeric,
    `prayer_notifications` integer DEFAULT '1' NOT NULL,
    `reminder_minutes` integer,
    `quran_translation` text DEFAULT 'both' NOT NULL,
    `arabic_text_size` text DEFAULT 'medium' NOT NULL,
    `daily_verse_goal` integer DEFAULT 5 NOT NULL,
    `enable_night_mode` integer DEFAULT '0' NOT NULL,
    `auto_mark_prayers` integer DEFAULT '0' NOT NULL,
    `congregation_points_bonus` integer DEFAULT '1' NOT NULL,
    `zakat_paid_this_year` integer DEFAULT '0',
    `zakat_paid_year` integer,
    `google_id` text
);
CREATE TABLE `password_reset_tokens` (
    `email` text PRIMARY KEY NOT NULL,
    `token` text NOT NULL,
    `created_at` numeric
);
CREATE TABLE `sessions` (
    `id` text PRIMARY KEY NOT NULL,
    `user_id` integer,
    `ip_address` text,
    `user_agent` text,
    `payload` text NOT NULL,
    `last_activity` integer NOT NULL
);
CREATE TABLE `verses` (
    `id` integer PRIMARY KEY,
    `surah_number` integer NOT NULL,
    `verse_number` integer NOT NULL,
    `arabic_text` text NOT NULL,
    `transliteration` text,
    `translation_english` text,
    `translation_bengali` text,
    `juz` integer,
    `page` integer,
    `revelation_type` text,
    `created_at` numeric,
    `updated_at` numeric
);
CREATE TABLE `user_verse_progress` (
    `id` integer PRIMARY KEY,
    `user_id` integer NOT NULL,
    `verse_id` integer NOT NULL,
    `is_read` integer DEFAULT '0' NOT NULL,
    `is_understood` integer DEFAULT '0' NOT NULL,
    `is_memorized` integer DEFAULT '0' NOT NULL,
    `read_at` numeric,
    `understood_at` numeric,
    `memorized_at` numeric,
    `review_count` integer DEFAULT 0 NOT NULL,
    `last_reviewed_at` numeric,
    `next_review_at` numeric,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_user_verse_progress_verse_id_verses_id_fk` FOREIGN KEY (`verse_id`) REFERENCES `verses`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_verse_progress_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
CREATE TABLE `prayer_logs` (
    `id` integer PRIMARY KEY,
    `user_id` integer NOT NULL,
    `prayer_date` numeric NOT NULL,
    `prayer_name` text NOT NULL,
    `is_completed` integer DEFAULT '0' NOT NULL,
    `completed_at` numeric,
    `on_time` integer DEFAULT '0' NOT NULL,
    `in_congregation` integer DEFAULT '0' NOT NULL,
    `at_mosque` integer DEFAULT '0' NOT NULL,
    `points_earned` integer DEFAULT 0 NOT NULL,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_prayer_logs_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT "prayer_logs_check_1" CHECK("prayer_name" in ('fajr', 'dhuhr', 'asr', 'maghrib', 'isha')) not null, 'is_completed' tinyint(1) not null default '0', 'completed_at' datetime, 'on_time' tinyint(1) not null default '0', 'in_congregation' tinyint(1) not null default '0', 'at_mosque' tinyint(1) not null default '0', 'points_earned' integer not null default '0', 'created_at' datetime, 'updated_at' datetime, foreign key('user_id') references 'users'('id') on delete cascade)
);
CREATE TABLE `surahs` (
    `id` integer PRIMARY KEY,
    `surah_number` integer NOT NULL,
    `name_arabic` text NOT NULL,
    `name_english` text NOT NULL,
    `name_transliteration` text NOT NULL,
    `total_verses` integer NOT NULL,
    `revelation_type` text NOT NULL,
    `revelation_order` integer,
    `description` text,
    `created_at` numeric,
    `updated_at` numeric
);
CREATE TABLE `daily_goals` (
    `id` integer PRIMARY KEY,
    `user_id` integer NOT NULL,
    `goal_date` numeric NOT NULL,
    `target_verses` integer DEFAULT 5 NOT NULL,
    `verses_completed` integer DEFAULT 0 NOT NULL,
    `all_prayers_completed` integer DEFAULT '0' NOT NULL,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_daily_goals_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
CREATE TABLE `hadith_collections` (
    `id` integer PRIMARY KEY,
    `name` text NOT NULL,
    `name_arabic` text NOT NULL,
    `description` text,
    `is_verified` integer DEFAULT '0' NOT NULL,
    `accuracy_percentage` numeric,
    `created_at` numeric,
    `updated_at` numeric
);
CREATE TABLE `hadith_chapters` (
    `id` integer PRIMARY KEY,
    `collection_id` integer NOT NULL,
    `chapter_number` numeric NOT NULL,
    `chapter_name_english` text NOT NULL,
    `chapter_name_arabic` text NOT NULL,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_hadith_chapters_collection_id_hadith_collections_id_fk` FOREIGN KEY (`collection_id`) REFERENCES `hadith_collections`(`id`) ON DELETE CASCADE
);
CREATE TABLE `hadith_sections` (
    `id` integer PRIMARY KEY,
    `chapter_id` integer NOT NULL,
    `section_number` numeric NOT NULL,
    `section_name_english` text,
    `section_name_arabic` text,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_hadith_sections_chapter_id_hadith_chapters_id_fk` FOREIGN KEY (`chapter_id`) REFERENCES `hadith_chapters`(`id`) ON DELETE CASCADE
);
CREATE TABLE `hadiths` (
    `id` integer PRIMARY KEY,
    `collection_id` integer NOT NULL,
    `chapter_id` integer NOT NULL,
    `section_id` integer,
    `hadith_number` numeric NOT NULL,
    `english_hadith` text NOT NULL,
    `english_isnad` text NOT NULL,
    `english_matn` text NOT NULL,
    `arabic_hadith` text NOT NULL,
    `arabic_isnad` text NOT NULL,
    `arabic_matn` text NOT NULL,
    `arabic_comment` text,
    `english_grade` text NOT NULL,
    `arabic_grade` text NOT NULL,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_hadiths_section_id_hadith_sections_id_fk` FOREIGN KEY (`section_id`) REFERENCES `hadith_sections`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_hadiths_chapter_id_hadith_chapters_id_fk` FOREIGN KEY (`chapter_id`) REFERENCES `hadith_chapters`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_hadiths_collection_id_hadith_collections_id_fk` FOREIGN KEY (`collection_id`) REFERENCES `hadith_collections`(`id`) ON DELETE CASCADE
);
CREATE TABLE `user_hadith_progress` (
    `id` integer PRIMARY KEY,
    `user_id` integer NOT NULL,
    `hadith_id` integer NOT NULL,
    `status` text NOT NULL,
    `read_at` numeric,
    `memorized_at` numeric,
    `notes` text,
    `created_at` numeric,
    `updated_at` numeric,
    CONSTRAINT `fk_user_hadith_progress_hadith_id_hadiths_id_fk` FOREIGN KEY (`hadith_id`) REFERENCES `hadiths`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_user_hadith_progress_user_id_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT "user_hadith_progress_check_2" CHECK("status" in ('not_read', 'read', 'memorized')) not null, 'read_at' datetime, 'memorized_at' datetime, 'notes' text, 'created_at' datetime, 'updated_at' datetime, foreign key('user_id') references 'users'('id') on delete cascade, foreign key('hadith_id') references 'hadiths'('id') on delete cascade)
);
CREATE UNIQUE INDEX `personal_access_tokens_token_unique` ON `personal_access_tokens` (`token`);
CREATE INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` ON `personal_access_tokens` (`tokenable_type`,`tokenable_id`);
CREATE UNIQUE INDEX `users_google_id_unique` ON `users` (`google_id`);
CREATE UNIQUE INDEX `users_email_unique` ON `users` (`email`);
CREATE INDEX `sessions_last_activity_index` ON `sessions` (`last_activity`);
CREATE INDEX `sessions_user_id_index` ON `sessions` (`user_id`);
CREATE INDEX `verses_juz_index` ON `verses` (`juz`);
CREATE INDEX `verses_surah_number_index` ON `verses` (`surah_number`);
CREATE UNIQUE INDEX `verses_surah_number_verse_number_unique` ON `verses` (`surah_number`,`verse_number`);
CREATE INDEX `user_verse_progress_user_id_is_memorized_index` ON `user_verse_progress` (`user_id`,`is_memorized`);
CREATE INDEX `user_verse_progress_user_id_index` ON `user_verse_progress` (`user_id`);
CREATE UNIQUE INDEX `user_verse_progress_user_id_verse_id_unique` ON `user_verse_progress` (`user_id`,`verse_id`);
CREATE INDEX `prayer_logs_user_id_prayer_date_index` ON `prayer_logs` (`user_id`,`prayer_date`);
CREATE INDEX `prayer_logs_prayer_date_index` ON `prayer_logs` (`prayer_date`);
CREATE INDEX `prayer_logs_user_id_index` ON `prayer_logs` (`user_id`);
CREATE UNIQUE INDEX `prayer_logs_user_id_prayer_date_prayer_name_unique` ON `prayer_logs` (`user_id`,`prayer_date`,`prayer_name`);
CREATE UNIQUE INDEX `surahs_surah_number_unique` ON `surahs` (`surah_number`);
CREATE INDEX `surahs_surah_number_index` ON `surahs` (`surah_number`);
CREATE INDEX `daily_goals_goal_date_index` ON `daily_goals` (`goal_date`);
CREATE INDEX `daily_goals_user_id_index` ON `daily_goals` (`user_id`);
CREATE UNIQUE INDEX `daily_goals_user_id_goal_date_unique` ON `daily_goals` (`user_id`,`goal_date`);
CREATE INDEX `hadiths_english_grade_index` ON `hadiths` (`english_grade`);
CREATE INDEX `hadiths_hadith_number_index` ON `hadiths` (`hadith_number`);
CREATE INDEX `hadiths_collection_id_chapter_id_index` ON `hadiths` (`collection_id`,`chapter_id`);
CREATE INDEX `user_hadith_progress_user_id_status_index` ON `user_hadith_progress` (`user_id`,`status`);
CREATE UNIQUE INDEX `user_hadith_progress_user_id_hadith_id_unique` ON `user_hadith_progress` (`user_id`,`hadith_id`);
```

## Developer: Redwan Rahman
- GitHub: github.com/Red1-Rahman
- Email: redwanrahman2002@outlook.com