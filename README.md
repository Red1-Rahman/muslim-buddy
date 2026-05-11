# Muslim Buddy

A Laravel web application for tracking personal Islamic practice — prayers, Quran reading, Zakat, and hadith study — with an integrated points and streak system.

---

## Overview

Muslim Buddy helps Muslims maintain consistency in their daily worship by combining structured tracking with spaced-repetition memorization and community leaderboards. Prayer times are calculated locally using astronomical formulas. Quran text, translations, and transliterations are seeded once from public APIs and stored locally — no runtime dependency after setup.

<img width="1920" height="1068" alt="Dashboard" src="https://github.com/user-attachments/assets/0ee37c5c-2598-4ca8-912d-92e52f99d4aa" />

---

## Features

### Log in / Sign up    

<img width="1910" height="926" alt="image" src="https://github.com/user-attachments/assets/f03f5ce0-2312-459a-ae17-34c7ffc51e9a" />


### Prayer Tracking

<img width="1920" height="1259" alt="Prayer page" src="https://github.com/user-attachments/assets/1c7350ca-11c8-4352-84b0-a19ad9171199" />

- Prayer times calculated from GPS coordinates using the Meeus astronomical algorithm
- Supports multiple calculation methods (Muslim World League, Egyptian, Karachi, Dubai, etc.) and both Shafi and Hanafi madhabs
- Log each prayer with quality flags: on-time, congregation, mosque
- Streak tracking and points awards (10–30 pts per prayer)
- Dashboard stats (today / this week / this month / current streak) update live on prayer completion

### Quran

<img width="1707" height="903" alt="Surah list" src="https://github.com/user-attachments/assets/2f0c62fb-e02c-448f-aaf0-175a851c44d3" />
<img width="1708" height="907" alt="Surah reading" src="https://github.com/user-attachments/assets/10fd2769-1d66-40a4-b75a-b468dec38185" />

- Full Quran — 114 surahs, 6,236 verses — seeded from the [Quran Foundation public API](https://api.quran.com/api/v4)
- Arabic text (Uthmani script), English translation (Dr. Mustafa Khattab), and transliteration stored locally after seeding
- Short description for each surah sourced from the chapter info API
- Audio recitation player on each surah page (Mishari Rashid al-Afasy, fetched at runtime)
- Per-verse progress states: Read (1 pt), Understood (2 pts), Memorized (5 pts)
- Spaced-repetition review scheduler with Easy / Medium / Hard difficulty
- Search across Arabic text, English translation, and transliteration

### Zakat Calculator
<img width="1920" height="2611" alt="screencapture-127-0-0-1-8080-zakat-2026-03-19-15_19_38" src="https://github.com/user-attachments/assets/e00a61c2-acfa-4c97-b25a-770f80e676d5" />
<img width="581" height="433" alt="image" src="https://github.com/user-attachments/assets/623760c5-bde2-4856-b5b7-5307638bc757" />

- Live Nisab thresholds fetched from [nisab.tahababa.com](https://nisab.tahababa.com) (free, no auth, updated 6× daily)
- Supports all four major schools: Hanafi, Maliki, Shafi'i, Hanbali — defaults to user's madhab from profile
- Currency support via [fawazahmed0/currency-api](https://github.com/fawazahmed0/currency-api): USD, GBP, EUR, BDT, SAR, MYR, IDR, PKR, TRY, EGP, NGN
- Client-side calculation (cash, gold, silver, inventory, receivables, debts)
- Hawl reminder and scholar consultation disclaimer included
- Personal Zakat payment record — deliberately excluded from the points and gamification system

### Hadith *(partial — under development)*

- Authentic collections: Sahih al-Bukhari, Sahih Muslim, and others
- Arabic text, English translation, Isnad (chain of narrators), and grade (Sahih / Hasan / Daif)
- Read and memorized tracking per hadith
- Currently seeded: Bukhari Chapter 2 only

### Gamification

![Prayer streak demo](https://github.com/user-attachments/assets/c2f4ba88-ee2a-4acc-97a4-9597331fb7e5)
<img width="875" height="257" alt="Points" src="https://github.com/user-attachments/assets/1c05fc79-1223-43c9-a0f0-7c685ac4ec2c" />
<img width="1674" height="785" alt="Leaderboard" src="https://github.com/user-attachments/assets/4b2dbfaf-0370-4200-b0c3-d21aecb3141f" />

- Unified points system across prayer, Quran, and hadith
- Leaderboards: overall, Quran progress, prayer completion, streaks (all-time / monthly / weekly)
- Daily goals with visual progress indicators

---

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 10 |
| Language | PHP 8.2+ |
| Database | SQLite (local dev) / MySQL or PostgreSQL (production) |
| Auth | Laravel session auth + Google OAuth + Quran.Foundation OAuth2/OIDC |
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

# 3. Run migrations
php artisan migrate --force

# 4. Seed core data
php artisan db:seed --class=SurahSeeder        # 114 surahs
php artisan db:seed --class=VerseSeeder        # 6,236 verses — Arabic, English, transliteration
php artisan db:seed --class=SurahInfoSeeder    # surah descriptions
php artisan db:seed --class=TestHadithSeeder   # sample hadith data

# 5. Start the development server
./start.sh
```

App is available at `http://127.0.0.1:8080`.

> **Note:** `VerseSeeder` makes three sequential passes against `api.quran.com` — Arabic text, English translation (resource ID 85, Dr. Mustafa Khattab), and word-by-word transliteration. All data is stored locally after seeding. The only runtime API calls after setup are audio recitation URLs (per surah, on page load) and live Nisab thresholds on the Zakat page.

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
| Karachi | 24.8607 | 67.0011 |
| Jakarta | -6.2088 | 106.8456 |

---

## Environment Variables

Key variables in `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# Quran seeding (public API, no auth required)
QURAN_API_BASE_URL=https://api.quran.com/api/v4
QURAN_API_SCRIPT=uthmani
QURAN_API_PER_PAGE=50
QURAN_API_MAX_PAGES=0                    # 0 = fetch all pages
QURAN_API_TRANSLATION_RESOURCE_ID=85    # Dr. Mustafa Khattab — The Clear Quran
QURAN_API_FETCH_TRANSLITERATION=true

# Quran audio (runtime, public API)
QURAN_RECITATION_ID=7                   # Mishari Rashid al-Afasy
```

---

## External APIs Used

| API | Purpose | Auth | When |
|---|---|---|---|
| [api.quran.com/api/v4](https://api.quran.com/api/v4) | Quran text, translations, transliteration, audio URLs | None | Seed-time + runtime (audio) |
| [nisab.tahababa.com](https://nisab.tahababa.com) | Live Zakat Nisab thresholds | None | Runtime |
| [fawazahmed0/currency-api](https://github.com/fawazahmed0/currency-api) | Currency conversion for Zakat calculator | None | Runtime |

---

## Project Structure

```
app/
├── Http/Controllers/     # QuranController, PrayerController, ZakatController,
│                         # HadithController, ProfileController
├── Models/               # User, Verse, Surah, PrayerLog, DailyGoal,
│                         # UserVerseProgress, Hadith
└── Services/
    ├── Astronomy/        # Meeus solar calculations
    ├── Prayer/           # Prayer time calculation engine
    ├── QuranFoundationOAuthService.php
    ├── QuranFoundationUserService.php
    └── QuranFoundationContentService.php

database/
├── migrations/
└── seeders/              # SurahSeeder, VerseSeeder, SurahInfoSeeder, HadithSeeders

resources/views/
├── quran/                # index, show, verse, search, reviews, statistics
├── zakat/                # calculator
├── prayers/              # index, statistics
├── hadith/               # index, show
└── layouts/              # app.blade.php

config/
└── services.php          # Quran recitation config

routes/
├── web.php
└── auth.php
start.sh                  # Safe local dev launcher (unsets conflicting shell DB vars)
```

---

## API Endpoints

| Method | Path | Description |
|---|---|---|
| `POST` | `/api/prayer-times` | Calculate prayer times for given coordinates |
| `GET` | `/api/user` | Authenticated user profile |

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

## Astronomical Calculation Method: Meeus Workflow

Prayer times are calculated from solar position using algorithms from *Astronomical Algorithms* by Jean Meeus. No external API is called at runtime.

### Step 1 — Julian Day

Convert calendar date to Julian Day.

$$\text{JD} = 367Y - \left\lfloor\frac{7(Y+\lfloor(M+9)/12\rfloor)}{4}\right\rfloor + \left\lfloor\frac{275M}{9}\right\rfloor + D + 1721013.5$$

### Step 2 — Julian Centuries

Meeus works heavily with:

$$T = \frac{\text{JD} - 2451545.0}{36525}$$

This measures time from the J2000 epoch.

### Step 3 — Solar Mean Longitude

$$L_0 = 280.46646 + 36000.76983T + 0.0003032T^2$$

### Step 4 — Solar Mean Anomaly

$$M = 357.52911 + 35999.05029T - 0.0001537T^2$$

### Step 5 — Equation of Center

$$C = (1.914602 - 0.004817T - 0.000014T^2)\sin M + (0.019993 - 0.000101T)\sin 2M + 0.000289\sin 3M$$

### Step 6 — True Solar Longitude

$$\lambda = L_0 + C$$

### Step 7 — Obliquity of Ecliptic

$$\epsilon = 23.439291 - 0.0130042T$$

### Step 8 — Solar Declination

Critical for prayer times.

$$\delta = \arcsin(\sin\epsilon\sin\lambda)$$

### Step 9 — Equation of Time

Needed for solar noon. Meeus derives this from right ascension, mean longitude, and nutation corrections to produce the difference between apparent solar time and mean civil time.

### Step 10 — Hour Angle

For any prayer altitude:

$$\cos H = \frac{\sin h - \sin\phi\sin\delta}{\cos\phi\cos\delta}$$

Where:
- $h$ = target solar altitude (in degrees)
- $\phi$ = observer latitude
- $\delta$ = solar declination

### Prayer Altitudes

| Prayer | Solar Altitude |
|---|---|
| Sunrise | −0.833° |
| Maghrib | −0.833° |
| Fajr | −18° (varies by method) |
| Isha | −17° or −18° |
| Asr | shadow geometry |

### Hour Angle to Time

Earth rotates 15° per hour. Convert hour angle to time offset:

$$t = \frac{H}{15}$$

Then:

$$\text{event time} = \text{solar noon} \pm t$$

### Convert UTC → Local Timezone

Meeus astronomy produces physical event timing in UTC. Then convert to local timezone using IANA timezone database, DST rules, and civil timezone laws to produce human-readable prayer times.

---

## License

MIT License. See `LICENSE` for details.

---

## Author

**Redwan Rahman** — [github.com/Red1-Rahman](https://github.com/Red1-Rahman)

---

*"And establish prayer and give zakah and bow with those who bow."* — Quran 2:43
