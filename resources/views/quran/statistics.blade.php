@extends('layouts.app')

@section('title', 'Quran Statistics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="Quran" class="h-10 w-10 mr-3"> 
                    Quran Statistics
                </h1>
                <p class="text-gray-600 mt-1">Detailed breakdown of your Quran progress</p>
            </div>
            <a href="{{ route('quran.index') }}" class="text-emerald-600 hover:text-emerald-800">
                ← Back to Quran
            </a>
        </div>
    </div>

    <!-- Overall Progress Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Read -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Verses Read</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_read']) }}</p>
                    <p class="text-blue-100 text-sm mt-1">{{ $stats['read_percentage'] }}% of Quran</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Understood -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Verses Understood</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_understood']) }}</p>
                    <p class="text-green-100 text-sm mt-1">{{ round(($stats['total_understood'] / $stats['total_verses']) * 100, 2) }}%</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-lightbulb text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Memorized -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Verses Memorized</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_memorized']) }}</p>
                    <p class="text-purple-100 text-sm mt-1">{{ $stats['memorized_percentage'] }}% of Quran</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-brain text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Due Reviews -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Due for Review</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['due_reviews']) }}</p>
                    <p class="text-orange-100 text-sm mt-1">
                        @if($stats['due_reviews'] > 0)
                            <a href="{{ route('quran.reviews') }}" class="underline hover:text-white">Review now</a>
                        @else
                            All caught up!
                        @endif
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress by Juz -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-list-ol text-emerald-600 mr-2"></i>
            Progress by Juz (Para)
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($juzProgress as $juz)
                @php
                    $percentage = $juz->total > 0 ? round(($juz->read / $juz->total) * 100, 1) : 0;
                @endphp
                <div class="border border-gray-200 rounded-lg p-4 hover:border-emerald-500 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-900">Juz {{ $juz->juz }}</h3>
                        <span class="text-sm font-medium text-emerald-600">{{ $percentage }}%</span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-300" 
                             style="width: {{ $percentage }}%"></div>
                    </div>
                    
                    <p class="text-sm text-gray-600">
                        {{ number_format($juz->read) }} / {{ number_format($juz->total) }} verses read
                    </p>
                </div>
            @endforeach
        </div>

        @if($juzProgress->isEmpty())
            <div class="text-center py-8">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="Quran" class="h-16 w-16 opacity-50">
                </div>
                <p class="text-gray-500">No progress data available yet.</p>
                <p class="text-sm text-gray-400 mt-2">Start reading verses to see your juz-by-juz progress!</p>
            </div>
        @endif
    </div>

    <!-- Milestones -->
    <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-trophy text-yellow-500 mr-2"></i>
            Milestones
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Reading Milestone -->
            <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                <h3 class="font-semibold text-gray-900 mb-2">Reading Progress</h3>
                @if($stats['total_read'] >= 6236)
                    <p class="text-green-600 font-medium">🎉 Completed entire Quran!</p>
                @elseif($stats['total_read'] >= 3118)
                    <p class="text-blue-600">Halfway through the Quran</p>
                @elseif($stats['total_read'] >= 1000)
                    <p class="text-blue-600">Over 1,000 verses read</p>
                @elseif($stats['total_read'] >= 100)
                    <p class="text-blue-600">100+ verses read</p>
                @else
                    <p class="text-gray-600">Keep reading to unlock milestones</p>
                @endif
            </div>

            <!-- Understanding Milestone -->
            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                <h3 class="font-semibold text-gray-900 mb-2">Understanding Progress</h3>
                @if($stats['total_understood'] >= 6236)
                    <p class="text-green-600 font-medium">🎉 Understood entire Quran!</p>
                @elseif($stats['total_understood'] >= 1000)
                    <p class="text-green-600">Over 1,000 verses understood</p>
                @elseif($stats['total_understood'] >= 100)
                    <p class="text-green-600">100+ verses understood</p>
                @else
                    <p class="text-gray-600">Study verses to unlock milestones</p>
                @endif
            </div>

            <!-- Memorization Milestone -->
            <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded">
                <h3 class="font-semibold text-gray-900 mb-2">Memorization Progress</h3>
                @if($stats['total_memorized'] >= 6236)
                    <p class="text-purple-600 font-medium">🎉 Hafiz - Entire Quran memorized!</p>
                @elseif($stats['total_memorized'] >= 600)
                    <p class="text-purple-600">Memorized a full Juz!</p>
                @elseif($stats['total_memorized'] >= 100)
                    <p class="text-purple-600">100+ verses memorized</p>
                @elseif($stats['total_memorized'] >= 10)
                    <p class="text-purple-600">10+ verses memorized</p>
                @else
                    <p class="text-gray-600">Memorize verses to unlock milestones</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 flex flex-wrap gap-4 justify-center">
        <a href="{{ route('quran.index') }}" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-colors">
            <i class="fas fa-book-open mr-2"></i>Continue Reading
        </a>
        
        @if($stats['due_reviews'] > 0)
            <a href="{{ route('quran.reviews') }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-colors">
                <i class="fas fa-clock mr-2"></i>Review {{ $stats['due_reviews'] }} Verses
            </a>
        @endif
        
        <a href="{{ route('quran.search') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition-colors">
            <i class="fas fa-search mr-2"></i>Search Quran
        </a>
    </div>
</div>
@endsection
