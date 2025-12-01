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

### 📖 Quran Progress Tracking

- **6,236 verses** with Arabic text, transliteration, and translations
- **Progress tracking**: Mark verses as read, understood, and memorized
- **Spaced repetition system** for memorization review
- **Search functionality** across Arabic text and translations
- **Surah-wise progress visualization**
- **Points system** for achievements

### 🏆 Gamification & Social Features

- **Leaderboards** for overall points, Quran progress, prayer completion, and streaks
- **Points system**: Reading (1pt), Understanding (2pts), Memorization (5pts), Prayers (10-30pts)
- **Daily goals** and progress tracking
- **Achievement badges** and milestones
- **Community ranking** and motivation

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
   php artisan migrate
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

1. **Browse Surahs**: View all 114 chapters with progress
2. **Read Verses**: Click on any surah to start reading
3. **Track Progress**: Mark verses as read, understood, or memorized
4. **Review System**: Get reminded to review memorized verses
5. **Search**: Find specific verses using the search feature

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
- **Verses**: 6,236 Quranic verses with translations
- **User Progress**: Reading, understanding, memorization tracking
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
│   ├── Http/Controllers/    # Web controllers
│   ├── Models/             # Eloquent models
│   └── Services/
│       ├── Astronomy/      # Astronomical calculations
│       └── Prayer/         # Prayer time calculations
├── database/
│   ├── migrations/         # Database schema
│   └── seeders/           # Sample data
├── resources/views/        # Blade templates
└── routes/                # Web and API routes
```

### Key Services

- **Astronomical**: Core astronomical calculations
- **SolarTime**: Solar position and time calculations
- **PrayerTimes**: Islamic prayer time calculator
- **CalculationMethod**: Various Islamic calculation methods

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

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
