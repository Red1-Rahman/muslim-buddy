@extends('layouts.app')

@section('title', 'Search Quran')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-emerald-900 mb-2">🔍 Search Quran</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Search through verses in Arabic text, English translation, and transliteration
            </p>
        </div>

        <!-- Back to Quran -->
        <div class="mb-6">
            <a href="{{ route('quran.index') }}" 
               class="text-emerald-600 hover:text-emerald-800 flex items-center">
                ← Back to Quran
            </a>
        </div>
            <!-- Search Form -->
            <div class="bg-white rounded-xl shadow-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('quran.search') }}" class="mb-4">
                        <div class="flex gap-4">
                            <input type="text" 
                                   name="q" 
                                   value="{{ $query }}" 
                                   placeholder="Search verses in Arabic, English, or transliteration..." 
                                   class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <button type="submit" 
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                🔍 Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            @if($query)
                <div class="bg-white rounded-xl shadow-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">
                            Search Results for "{{ $query }}" ({{ $verses->count() }} found)
                        </h3>

                        @if($verses->count() > 0)
                            <div class="space-y-6">
                                @foreach($verses as $verse)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="text-sm text-gray-600">
                                                {{ $verse->surah->name_english }} ({{ $verse->surah->name_arabic }}) 
                                                - Verse {{ $verse->verse_number }}
                                            </div>
                                            <div class="flex space-x-2">
                                                @php
                                                    $progress = $verse->user_progress;
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $progress && $progress->is_read ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $progress && $progress->is_read ? 'Read' : 'Unread' }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $progress && $progress->is_understood ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $progress && $progress->is_understood ? 'Understood' : 'Not Understood' }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $progress && $progress->is_memorized ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $progress && $progress->is_memorized ? 'Memorized' : 'Not Memorized' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Arabic Text -->
                                        <div class="text-2xl arabic-text mb-3 text-right leading-loose" dir="rtl">
                                            {{ $verse->arabic_text }}
                                        </div>

                                        <!-- Translation -->
                                        <div class="text-gray-700 mb-2">
                                            {{ $verse->translation_english }}
                                        </div>

                                        <!-- Transliteration -->
                                        @if($verse->transliteration)
                                            <div class="text-sm text-gray-500 italic">
                                                {{ $verse->transliteration }}
                                            </div>
                                        @endif

                                        <!-- Action Buttons -->
                                        <div class="mt-4">
                                            <a href="{{ route('quran.show', $verse->surah->surah_number) }}#verse-{{ $verse->verse_number }}" 
                                               class="inline-flex items-center text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                                <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="" class="h-4 w-4 inline mr-1"> View in Surah Context →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                No verses found matching your search query.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500 py-8">
                            <div class="flex justify-center mb-4">
                                <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="Quran" class="h-16 w-16">
                            </div>
                            <p>Enter a search term to find verses in the Quran.</p>
                            <p class="text-sm mt-2">You can search in Arabic text, English translation, or transliteration.</p>
                        </div>
                    </div>
                </div>
            @endif
    </div>
</div>

<style>
    .arabic-text {
        font-family: 'Amiri', 'Scheherazade New', serif;
    }
</style>
@endsection