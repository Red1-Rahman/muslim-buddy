@extends('layouts.app')

@section('title', 'Quran')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-book-quran text-indigo-600"></i> Holy Quran
        </h1>
        <p class="text-gray-600 mt-1">Track your reading, understanding, and memorization progress</p>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Progress</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="relative inline-block">
                    <svg class="w-20 h-20" viewBox="0 0 36 36">
                        <path class="text-gray-300" stroke="currentColor" stroke-width="2" fill="none"
                              d="M18 2.0845 A 15.9155 15.9155 0 0 1 18 33.9155 A 15.9155 15.9155 0 0 1 18 2.0845"/>
                        <path class="text-blue-600" stroke="currentColor" stroke-width="2" fill="none"
                              stroke-dasharray="{{ $user->quran_progress_percentage }}, 100"
                              d="M18 2.0845 A 15.9155 15.9155 0 0 1 18 33.9155 A 15.9155 15.9155 0 0 1 18 2.0845"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-semibold">
                        {{ round($user->quran_progress_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-gray-700 font-medium mt-2">Read</p>
            </div>
            
            <div class="text-center">
                <div class="relative inline-block">
                    <svg class="w-20 h-20" viewBox="0 0 36 36">
                        <path class="text-gray-300" stroke="currentColor" stroke-width="2" fill="none"
                              d="M18 2.0845 A 15.9155 15.9155 0 0 1 18 33.9155 A 15.9155 15.9155 0 0 1 18 2.0845"/>
                        <path class="text-green-600" stroke="currentColor" stroke-width="2" fill="none"
                              stroke-dasharray="{{ $user->memorization_progress_percentage }}, 100"
                              d="M18 2.0845 A 15.9155 15.9155 0 0 1 18 33.9155 A 15.9155 15.9155 0 0 1 18 2.0845"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-semibold">
                        {{ round($user->memorization_progress_percentage, 1) }}%
                    </span>
                </div>
                <p class="text-gray-700 font-medium mt-2">Memorized</p>
            </div>
            
            <div class="text-center">
                <i class="fas fa-trophy text-yellow-500 text-4xl mb-2"></i>
                <p class="text-2xl font-bold text-gray-900">{{ $user->total_points }}</p>
                <p class="text-gray-600">Total Points</p>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form action="{{ route('quran.search') }}" method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="q" placeholder="Search verses, translations, or transliterations..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-search mr-2"></i> Search
            </button>
        </form>
    </div>

    <!-- Surahs List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($surahs as $surah)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">{{ $surah->surah_number }}. {{ $surah->name_english }}</h3>
                        <p class="text-indigo-100 text-sm">{{ $surah->name_transliteration }}</p>
                    </div>
                    <div class="text-right arabic-text text-xl">
                        {{ $surah->name_arabic }}
                    </div>
                </div>
            </div>
            
            <div class="p-4">
                <div class="flex justify-between text-sm text-gray-600 mb-3">
                    <span>{{ $surah->total_verses }} verses</span>
                    <span class="capitalize">{{ strtolower($surah->revelation_type) }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mb-3">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-600">Progress</span>
                        <span class="text-gray-600">{{ $surah->read_count }}/{{ $surah->total_verses }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $surah->progress }}%"></div>
                    </div>
                </div>
                
                <a href="{{ route('quran.show', $surah->surah_number) }}" 
                   class="block w-full text-center bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                    <i class="fas fa-book-open mr-2"></i> Read Surah
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('quran.reviews') }}" class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-clock text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Due Reviews</h3>
            <p class="text-sm opacity-90 mt-1">Review your memorized verses</p>
        </a>

        <a href="{{ route('quran.statistics') }}" class="bg-gradient-to-r from-green-400 to-teal-500 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-chart-bar text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Statistics</h3>
            <p class="text-sm opacity-90 mt-1">View detailed progress analytics</p>
        </a>
    </div>
</div>
@endsection