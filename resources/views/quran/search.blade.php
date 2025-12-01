<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Quran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('quran.search') }}" class="mb-4">
                        <div class="flex gap-4">
                            <input type="text" 
                                   name="query" 
                                   value="{{ $query }}" 
                                   placeholder="Search verses..." 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            @if($query)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">
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
                                                    $progress = $verse->progress->first();
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
                                        <div class="text-2xl arabic-text mb-3 text-right leading-loose">
                                            {{ $verse->text_arabic }}
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
                                        <div class="flex space-x-2 mt-4">
                                            <form method="POST" action="{{ route('quran.progress.update', $verse->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="type" value="read">
                                                <button type="submit" 
                                                        class="text-sm px-3 py-1 rounded {{ $progress && $progress->is_read ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white' }}">
                                                    {{ $progress && $progress->is_read ? 'Mark Unread' : 'Mark Read' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('quran.progress.update', $verse->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="type" value="understood">
                                                <button type="submit" 
                                                        class="text-sm px-3 py-1 rounded {{ $progress && $progress->is_understood ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-500 hover:text-white' }}">
                                                    {{ $progress && $progress->is_understood ? 'Mark Not Understood' : 'Mark Understood' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('quran.progress.update', $verse->id) }}" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="type" value="memorized">
                                                <button type="submit" 
                                                        class="text-sm px-3 py-1 rounded {{ $progress && $progress->is_memorized ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-purple-500 hover:text-white' }}">
                                                    {{ $progress && $progress->is_memorized ? 'Mark Not Memorized' : 'Mark Memorized' }}
                                                </button>
                                            </form>
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 text-center">
                        <div class="text-gray-500 py-8">
                            Enter a search term to find verses in the Quran.
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
</x-app-layout>