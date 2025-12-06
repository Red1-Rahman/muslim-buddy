<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Muslim Buddy') }}</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gradient-to-br from-green-50 via-blue-50 to-purple-50">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('Muslim Buddy.svg') }}" alt="Muslim Buddy Logo" class="h-12 w-12">
                            <h1 class="text-2xl font-bold text-gray-900">Muslim Buddy</h1>
                        </div>
                        
                        <nav class="flex items-center space-x-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-600 hover:text-gray-900 font-medium">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4 py-2">Login</a>
                                <a href="{{ route('register') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-medium">Register</a>
                            @endauth
                        </nav>
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <main>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                            Welcome to <span class="text-green-600">Muslim Buddy</span>
                        </h1>
                        <p class="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            Your comprehensive spiritual companion for prayer times, Quran study, hadith collections, and Islamic practices
                        </p>
                        
                        @guest
                            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                                <a href="{{ route('register') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-semibold text-lg inline-block min-w-[200px] text-center">
                                    Start Your Journey
                                </a>
                                <a href="{{ route('login') }}" class="border border-green-600 text-green-600 px-8 py-3 rounded-lg hover:bg-green-50 font-semibold text-lg inline-block min-w-[200px] text-center">
                                    Sign In
                                </a>
                            </div>
                        @else
                            <a href="{{ route('dashboard') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-semibold text-lg inline-block">
                                Go to Dashboard
                            </a>
                        @endguest
                    </div>
                </div>

                <!-- Features Section -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="grid md:grid-cols-4 gap-8">
                        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
                            <div class="w-16 h-16 mx-auto mb-4 text-green-600">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Prayer Times</h3>
                            <p class="text-gray-600">Accurate prayer times based on your location with astronomical calculations and multiple calculation methods.</p>
                        </div>
                        
                        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
                            <div class="w-16 h-16 mx-auto mb-4 text-blue-600">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Quran Study</h3>
                            <p class="text-gray-600">Track your Quran reading progress, mark verses as read, understood, and memorized.</p>
                        </div>

                        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
                            <div class="w-16 h-16 mx-auto mb-4 text-amber-600">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Hadith Collections</h3>
                            <p class="text-gray-600">Study authentic sayings of Prophet Muhammad (ﷺ) from verified collections like Sahih Bukhari.</p>
                        </div>
                        
                        <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
                            <div class="w-16 h-16 mx-auto mb-4 text-purple-600">
                                <svg fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Progress Tracking</h3>
                            <p class="text-gray-600">Earn points, maintain streaks, and compete with others in your spiritual journey.</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-gray-800 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="text-center">
                        <p class="text-lg font-semibold mb-2">Muslim Buddy</p>
                        <p class="text-gray-400 mb-4">Developed by Redwan Rahman</p>
                        <p class="text-gray-400">
                            <a href="https://github.com/Red1-Rahman" class="hover:text-white">github.com/Red1-Rahman</a>
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>