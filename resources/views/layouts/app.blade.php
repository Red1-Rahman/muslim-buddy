<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muslim Buddy') - Developed by Redwan Rahman</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 1.75rem;
            line-height: 2.5rem;
            direction: rtl;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    @auth
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <img src="{{ asset('Muslim Buddy.svg') }}" alt="Muslim Buddy Logo" class="h-10 w-10">
                            <span class="text-2xl font-bold text-emerald-600">Muslim Buddy</span>
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('prayers.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-prayer-hands mr-1"></i> Prayers
                        </a>
                        <a href="{{ route('quran.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-book-quran mr-1"></i> Quran
                        </a>
                        <a href="{{ route('hadith.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-scroll mr-1"></i> Hadiths
                        </a>
                        <a href="{{ route('leaderboard.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-trophy mr-1"></i> Leaderboard
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center mr-4">
                        <i class="fas fa-coins text-yellow-500 mr-1"></i>
                        <span class="font-semibold">{{ auth()->user()->total_points }}</span>
                    </div>
                    <div class="flex items-center mr-4">
                        <i class="fas fa-fire text-orange-500 mr-1"></i>
                        <span class="font-semibold">{{ auth()->user()->prayer_streak }} days</span>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Page Content -->
    <main class="py-8">
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white mt-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © 2025 Muslim Buddy - Developed by <a href="https://github.com/Red1-Rahman" target="_blank" class="text-indigo-600 hover:text-indigo-800">Redwan Rahman</a>
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
