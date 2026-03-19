# Muslim Buddy

A Laravel web application for tracking personal Islamic practice — prayers, Quran reading, and hadith study — with an integrated points and streak system.

---

## Overview

Muslim Buddy helps Muslims maintain consistency in their daily worship by combining structured tracking with spaced-repetition memorization and community leaderboards. Prayer times are calculated locally using astronomical formulas; no third-party API is required for that core functionality.

---

## Features

### Prayer Tracking
- Prayer times calculated from GPS coordinates using the Meeus astronomical algorithm
- Supports multiple calculation methods (Muslim World League, Egyptian, Karachi, Dubai, etc.) and both Shafi and Hanafi madhabs
- Log each prayer with quality flags: on-time, congregation, mosque
- Streak tracking and points awards (10–30 pts per prayer)

### Quran
- Full Quran — 114 surahs, 6,236 verses — sourced via the [Quran Foundation API](https://api.quran.com/api/v4)
- Arabic text (Uthmani script) stored locally after seeding; no runtime API dependency
- Per-verse progress states: Read (1 pt), Understood (2 pts), Memorized (5 pts)
- Spaced-repetition review scheduler with Easy / Medium / Hard difficulty
- Search across Arabic text, English translation, and transliteration

### Hadith
- Authentic collections: Sahih al-Bukhari, Sahih Muslim, and others
- Arabic text, English translation, Isnad (chain of narrators), and grade (Sahih / Hasan / Daif)
- Read and memorized tracking per hadith

### Gamification
- Unified points system across all practice categories
- Leaderboards: overall, Quran progress, prayer completion, streaks (all-time / monthly / weekly)
- Daily goals with visual progress indicators

---

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 10 |
| Language | PHP 8.2+ |
| Database | SQLite (local dev) / MySQL or PostgreSQL (production) |
| Auth | Laravel built-in session auth |
| HTTP Client | Laravel HTTP (Guzzle) |
| Frontend | Blade templates, Tailwind CSS |

---

## Requirements

- PHP 8.2+ with `pdo_sqlite` extension
- Composer

---

## Local Setup

```bash
# 1. Clone and install dependencies
git clone https://github.com/Red1-Rahman/muslim-buddy.git
cd muslim-buddy
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Run migrations and seed core data
php artisan migrate --force
php artisan db:seed --class=SurahSeeder
php artisan db:seed --class=VerseSeeder   # fetches full Quran from api.quran.com
php artisan db:seed --class=TestHadithSeeder

# 4. Start the development server
./start.sh   # unsets conflicting shell DB vars, then runs php artisan serve
```

App is available at `http://127.0.0.1:8080`.

> **Note:** `VerseSeeder` fetches all 6,236 verses from `https://api.quran.com/api/v4` (public, no key required) and stores them locally. Run it once; subsequent app usage is offline.

---

## Location Configuration

Prayer times require your coordinates. After registering:

1. Go to **Profile → Settings**
2. Enter latitude, longitude, timezone
3. Select calculation method and madhab

Common coordinates for reference:

| City | Latitude | Longitude |
|---|---|---|
| Dhaka | 23.8103 | 90.4125 |
| Mecca | 21.4225 | 39.8262 |
| Cairo | 30.0444 | 31.2357 |
| London | 51.5072 | -0.1276 |

---

## Environment Variables

Key variables in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

QURAN_API_BASE_URL=https://api.quran.com/api/v4
QURAN_API_SCRIPT=uthmani
QURAN_API_PER_PAGE=50
QURAN_API_MAX_PAGES=0   # 0 = fetch all pages
```

---

## Project Structure

```
app/
├── Http/Controllers/     # QuranController, PrayerController, HadithController
├── Models/               # User, Verse, Surah, PrayerLog, UserVerseProgress
└── Services/
    ├── Astronomy/        # Meeus solar calculations
    └── Prayer/           # Prayer time calculation engine

database/
├── migrations/
└── seeders/              # SurahSeeder, VerseSeeder (API-backed), HadithSeeders

resources/views/          # Blade templates per feature (quran, prayer, hadith)
routes/
├── web.php
└── auth.php
start.sh                  # Safe local dev launcher
```

---

## API

Two endpoints are available for external integrations:

| Method | Path | Description |
|---|---|---|
| `POST` | `/api/prayer-times` | Calculate prayer times for given coordinates |
| `GET` | `/api/user` | Authenticated user profile (requires session) |

---

## Deployment

Set these additional environment variables in production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

Then:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
```

Tested deployment targets: Railway (recommended), Heroku, Render. Any platform supporting PHP 8.2+ and a persistent filesystem or external database works.

---

## Astronomical Calculation Method

Prayer times are derived from solar position using algorithms from *Astronomical Algorithms* by Jean Meeus:

- Julian Day and Julian Century from calendar date
- Solar longitude, mean anomaly, and equation of center
- Apparent sidereal time and solar declination
- Nutation in longitude and obliquity of the ecliptic
- Hour angle → prayer time conversion per calculation method

No external API is called at runtime for prayer times.

---

## License

MIT License. See `LICENSE` for details.

---

## Author

**Redwan Rahman** — [github.com/Red1-Rahman](https://github.com/Red1-Rahman)

---

*"And establish prayer and give zakah and bow with those who bow."* — Quran 2:43