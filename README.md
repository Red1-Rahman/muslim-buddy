# Muslim Buddy - Islamic Companion Web Application

**Developed by Redwan Rahman** ([github.com/Red1-Rahman](https://github.com/Red1-Rahman))

A comprehensive Laravel web application for Muslims to track their spiritual journey, featuring prayer times calculation using astronomical formulas, Quran progress tracking, and community leaderboards.

## 🌟 Features

### 🕌 Prayer Times & Tracking

- **Accurate prayer times** calculated using astronomical formulas (solar longitude, declination, sidereal time)
- **Real-time Julian day** and calendar handling
- **Multiple calculation methods**: Muslim World League, Egyptian, Karachi, Dubai, etc.
- **Madhab support**: Shafi and Hanafi
- **Forbidden prayer times** (Makruh times) detection
- **Prayer completion tracking** with points system
- **Prayer streaks** and quality tracking (on-time, congregation, mosque)
- **Automatic timezone detection** and location-based calculations

### 📖 Quran Progress Tracking

- **Complete Quran**: All 114 surahs with Al-Fatiha verses fully implemented
- **6,236 verses** with Arabic text, transliteration, and English/Bengali translations
- **Progress tracking**: Mark verses as read, understood, and memorized
- **Spaced repetition system** for memorization review with difficulty levels
- **Advanced search functionality** across Arabic text, English translation, and transliteration
- **Surah-wise progress visualization** with completion percentages
- **Points system**: Reading (1pt), Understanding (2pts), Memorization (5pts)
- **JSON-based verse storage** for efficient data management

### 📜 Hadith Collections

- **Authentic hadith collections**: Sahih al-Bukhari, Sahih Muslim, and more
- **Chapter-wise organization** with sections and proper categorization
- **Arabic text with English translations** and chain of narrators (Isnad)
- **Search and filter functionality** by collection, chapter, grade, and text
- **Progress tracking** for hadith study (read, memorized status)
- **Grade verification**: Sahih (Authentic), Hasan (Good), Daif (Weak)
- **CSV import system** for large hadith databases

### 🏆 Gamification & Social Features

- **Comprehensive leaderboards** for points, Quran progress, prayer completion, and streaks
- **Achievement system**: Reading (1pt), Understanding (2pts), Memorization (5pts), Prayers (10-30pts)
- **Daily goals** and progress tracking with visual indicators
- **User statistics** with detailed breakdowns by category
- **Community ranking** and motivation features

### 🔭 Advanced Astronomical Calculations

Based on "Astronomical Algorithms" by Jean Meeus:

- Solar longitude and declination
- Apparent sidereal time calculation
- Nutation in longitude and obliquity
- Julian day and century calculations
- Solar coordinates and time equations

## 🚀 Installation & Setup

### Prerequisites

- XAMPP with PHP 8.2+ and MySQL
- Composer (PHP package manager)

### Installation Steps

1. **Clone/Download the project**

   ```bash
   # If you have git:
   git clone https://github.com/Red1-Rahman/muslim-buddy.git
   # Or extract the downloaded ZIP file
   ```

2. **Navigate to project directory**

   ```bash
   cd muslim-buddy-laravel
   ```

3. **Install Composer dependencies**

   ```bash
   # Download Composer if not installed: https://getcomposer.org/download/
   composer install
   ```

4. **Setup environment**

   ```bash
   # Copy environment file
   copy .env.example .env

   # Generate application key
   php artisan key:generate
   ```

5. **Configure database**

   - Start XAMPP (Apache & MySQL)
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create database named `muslim_buddy`
   - Update `.env` file with your database credentials:

   ```env
   DB_DATABASE=muslim_buddy
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations and seeders**

   ```bash
   # Run database migrations
   php artisan migrate

   # Seed basic data (surahs, collections, sample data)
   php artisan db:seed --class=SurahSeeder
   php artisan db:seed --class=AlFatihaVerseSeeder
   php artisan db:seed --class=TestHadithSeeder
   php artisan db:seed --class=BukhariChapter2Seeder

   # Or run all seeders at once
   php artisan db:seed
   ```

7. **Start the application**

   ```bash
   # Start Laravel development server
   php artisan serve --host=0.0.0.0 --port=8000
   ```

8. **Access the application**
   - Open browser and go to: `http://localhost:8000`
   - Register a new account or login

### Configuration

#### Setting Your Location

1. Go to Profile settings
2. Enter your latitude and longitude
3. Choose calculation method and madhab
4. Prayer times will be calculated for your location

#### Default Locations

- **Dhaka, Bangladesh**: 23.8103°N, 90.4125°E
- **Mecca, Saudi Arabia**: 21.4225°N, 39.8262°E
- **Palestine (Jerusalem)**: 31.7683°N, 35.2137°E
- **Sudan (Khartoum)**: 15.5007°N, 32.5599°E
- **Congo (Kinshasa)**: -4.4419°S, 15.2663°E

## 🎯 Usage Guide

### Prayer Tracking

1. **View Today's Prayers**: Go to Prayers section
2. **Mark Complete**: Click on prayer name to mark as completed
3. **Quality Tracking**: Mark if prayer was on-time, in congregation, or at mosque
4. **View Statistics**: Track your prayer completion rates and streaks

### Quran Progress

1. **Browse Surahs**: View all 114 chapters with progress indicators
2. **Read Verses**: Click on any surah to start reading with Arabic text and translations
3. **Track Progress**: Mark verses as read, understood, or memorized
4. **Review System**: Use spaced repetition to review memorized verses
   - Easy: Normal intervals (1, 3, 7, 14, 30, 60 days)
   - Medium: Shortened intervals for challenging verses
   - Hard: Reset to 1 day for forgotten verses
5. **Search**: Find specific verses using Arabic text, English translation, or transliteration
6. **Statistics**: View detailed progress by surah, juz, and overall completion

### Hadith Study

1. **Browse Collections**: Access authentic hadith collections (Bukhari, Muslim, etc.)
2. **Chapter Navigation**: Browse by chapters and sections within collections
3. **Read Hadiths**: View Arabic text with English translations and chain of narrators
4. **Filter & Search**: Find hadiths by text, grade (Sahih/Hasan/Daif), or collection
5. **Track Progress**: Mark hadiths as read or memorized
6. **Verification**: See authenticity grades and collection verification status

### Leaderboards

1. **Overall Rankings**: View top performers by total points
2. **Category Rankings**: Filter by Quran progress, prayers, or streaks
3. **Time Periods**: View all-time, monthly, or weekly leaders
4. **Your Rank**: See where you stand in the community

## 🔧 Technical Details

### Astronomical Calculations

The application implements precise Islamic prayer time calculations using:

- **Meeus algorithms** for solar position
- **Geographic coordinates** for location-specific times
- **Various calculation methods** following different Islamic authorities
- **High latitude adjustments** for polar regions
- **Real-time Julian day** calculations

### Database Schema

- **Users**: Profile, location, preferences, points, streaks
- **Surahs**: All 114 chapters with metadata (names, verse counts, revelation type)
- **Verses**: Complete Quran with Arabic text, translations, transliteration
- **User Verse Progress**: Reading, understanding, memorization tracking with spaced repetition
- **Hadith Collections**: Major authentic collections with verification status
- **Hadith Chapters & Sections**: Organized structure for hadith navigation
- **Hadiths**: Full hadith texts with Arabic, English, Isnad, and authenticity grades
- **User Hadith Progress**: Study tracking for hadith collections
- **Prayer Logs**: Daily prayer completion records
- **Daily Goals**: Target setting and achievement tracking

### API Endpoints

```
POST /api/prayer-times - Get prayer times for coordinates
GET /api/user - Get authenticated user details (requires authentication)
```

## 🛠️ Development

### Project Structure

```
muslim-buddy-laravel/
├── app/
│   ├── Http/Controllers/    # Web controllers (Quran, Prayer, Hadith, User)
│   ├── Models/             # Eloquent models (User, Verse, Hadith, Progress)
│   └── Services/
│       ├── Astronomy/      # Astronomical calculations
│       └── Prayer/         # Prayer time calculations
├── database/
│   ├── migrations/         # Database schema (users, verses, hadiths, progress)
│   └── seeders/           # Data seeders (Quran, hadiths, sample data)
├── resources/
│   ├── views/             # Blade templates (quran, hadith, prayer, auth)
│   └── data/              # JSON files (Al-Fatiha verses, surah data)
├── routes/                # Web and API routes
└── public/               # Assets and entry point
```

### Key Services

- **Astronomical**: Core astronomical calculations for accurate prayer times
- **SolarTime**: Solar position and time calculations using Meeus algorithms
- **PrayerTimes**: Islamic prayer time calculator with multiple methods
- **CalculationMethod**: Various Islamic calculation methods (MWL, Egyptian, Karachi, etc.)
- **UserVerseProgress**: Spaced repetition system for Quran memorization
- **HadithController**: Comprehensive hadith browsing and study system

## 🚀 Deployment Options

### Free Hosting Platforms

The application can be deployed for free on several platforms:

1. **Railway** (Recommended)

   - Excellent Laravel support
   - Automatic deployments from GitHub
   - Includes PostgreSQL database
   - 500 hours/month + $5 credit

2. **Vercel**

   - Great performance and GitHub integration
   - Requires external database (PlanetScale)
   - Unlimited personal projects

3. **Heroku**
   - Popular choice for Laravel apps
   - Low-cost options starting at $5-7/month
   - PostgreSQL add-on available

### Deployment Steps

1. **Prepare for production**

   ```bash
   # Set environment variables
   APP_ENV=production
   APP_DEBUG=false

   # Optimize application
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Database setup**

   - Run migrations: `php artisan migrate --force`
   - Seed essential data: `php artisan db:seed --force`

3. **Configure environment variables** on your hosting platform:
   - Database credentials
   - App key and URL
   - Any third-party service keys

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📈 Current Status

**Phase 1: Core Features ✅ Completed**

- ✅ Prayer time calculations with astronomical accuracy
- ✅ Complete Quran (114 surahs) with progress tracking
- ✅ Al-Fatiha verses with Arabic text, translation, transliteration
- ✅ Spaced repetition system for memorization
- ✅ Hadith collections (Bukhari Chapter 2 implemented)
- ✅ User authentication and progress tracking
- ✅ Responsive UI with Islamic design elements

**Phase 2: Data Expansion 🔄 In Progress**

- 🔄 Populating remaining 113 surah JSON files
- 🔄 Expanding hadith collections (Muslim, Tirmidhi, etc.)
- 🔄 Adding more language translations

**Phase 3: Advanced Features 📋 Planned**

- 📋 Mobile app companion
- 📋 Offline functionality
- 📋 Community features and discussions
- 📋 Advanced analytics and insights

## 📄 License

This project is open-sourced under the MIT License.

## 👨‍💻 Developer

**Redwan Rahman**

- GitHub: [Red1-Rahman](https://github.com/Red1-Rahman)
- Islamic astronomy and prayer time calculation specialist
- Full-stack Laravel developer

## 🙏 Acknowledgments

- **Jean Meeus** - "Astronomical Algorithms" book
- **Islamic Society of North America** - Prayer time calculation methods
- **Quran.com** - Quranic text and translations
- **Laravel Community** - Framework and packages

---

_May Allah accept our efforts and make this application beneficial for the Muslim community. Ameen._

**"And establish prayer and give zakah and bow with those who bow [in worship and obedience]."** - Quran 2:43
