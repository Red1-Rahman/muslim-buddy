@extends('layouts.app')

@section('title', $surah->name_english . ' - Quran')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Surah Header -->
            <div class="text-center mb-8">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-emerald-200">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $surah->name_english }}</h1>
                    <h2 class="text-3xl font-arabic text-emerald-700 mb-2" dir="rtl">{{ $surah->name_arabic }}</h2>
                    <p class="text-lg text-gray-600 mb-4">{{ $surah->name_transliteration }}</p>
                    <div class="flex justify-center items-center space-x-8 text-sm text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-book-open mr-2 text-emerald-600"></i>
                            {{ $surah->total_verses }} Verses
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-emerald-600"></i>
                            {{ $surah->revelation_type }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-hashtag mr-2 text-emerald-600"></i>
                            Surah {{ $surah->surah_number }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Bismillah (except for At-Tawbah) -->
            @if($surah->surah_number != 9)
            <div class="text-center mb-8">
                <div class="bg-gradient-to-r from-emerald-600 to-blue-600 text-white rounded-xl p-6">
                    <h3 class="text-2xl font-arabic mb-2" dir="rtl">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</h3>
                    <p class="text-emerald-100">In the name of Allah, the Most Gracious, the Most Merciful</p>
                </div>
            </div>
            @endif

            <!-- Verses -->
            <div class="space-y-6">
                @foreach($verses as $verse)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="p-6">
                        <!-- Verse Number -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-emerald-100 text-emerald-800 rounded-full flex items-center justify-center font-bold">
                                    {{ $verse->verse_number }}
                                </div>
                                <span class="text-sm text-gray-500">
                                    Verse {{ $verse->verse_number }} | Juz {{ $verse->juz ?? 'N/A' }} | Page {{ $verse->page ?? 'N/A' }}
                                </span>
                            </div>
                            
                            <!-- Progress Buttons -->
                            <div class="flex space-x-2">
                                <button 
                                    onclick="markProgress({{ $verse->id }}, 'read')"
                                    class="progress-btn {{ $verse->user_progress?->is_read ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-600' }} px-3 py-1 rounded-lg text-xs font-medium transition-colors hover:bg-green-500">
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ $verse->user_progress?->is_read ? 'Read' : 'Mark Read' }}
                                </button>
                                
                                <button 
                                    onclick="markProgress({{ $verse->id }}, 'understood')"
                                    class="progress-btn {{ $verse->user_progress?->is_understood ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }} px-3 py-1 rounded-lg text-xs font-medium transition-colors hover:bg-blue-500">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    {{ $verse->user_progress?->is_understood ? 'Understood' : 'Mark Understood' }}
                                </button>
                                
                                <button 
                                    onclick="markProgress({{ $verse->id }}, 'memorized')"
                                    class="progress-btn {{ $verse->user_progress?->is_memorized ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-600' }} px-3 py-1 rounded-lg text-xs font-medium transition-colors hover:bg-purple-500">
                                    <i class="fas fa-brain mr-1"></i>
                                    {{ $verse->user_progress?->is_memorized ? 'Memorized' : 'Mark Memorized' }}
                                </button>
                            </div>
                        </div>

                        <!-- Arabic Text -->
                        <div class="mb-4">
                            <p class="text-2xl font-arabic leading-loose text-right text-gray-800 mb-3" dir="rtl">
                                {{ $verse->arabic_text }}
                            </p>
                        </div>

                        <!-- Transliteration -->
                        @if($verse->transliteration)
                        <div class="mb-3">
                            <p class="text-sm italic text-gray-600 bg-gray-50 p-3 rounded-lg">
                                <strong>Transliteration:</strong> {{ $verse->transliteration }}
                            </p>
                        </div>
                        @endif

                        <!-- English Translation -->
                        <div class="mb-4">
                            <p class="text-gray-700 leading-relaxed">
                                <strong>Translation:</strong> {{ $verse->translation_english }}
                            </p>
                        </div>

                        <!-- Progress Points -->
                        @if($verse->user_progress && ($verse->user_progress->is_read || $verse->user_progress->is_understood || $verse->user_progress->is_memorized))
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                @if($verse->user_progress->is_read)
                                <span class="flex items-center text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    +1 point (Read)
                                </span>
                                @endif
                                @if($verse->user_progress->is_understood)
                                <span class="flex items-center text-blue-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    +3 points (Understood)
                                </span>
                                @endif
                                @if($verse->user_progress->is_memorized)
                                <span class="flex items-center text-purple-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    +5 points (Memorized)
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Navigation -->
            <div class="mt-12 flex justify-between items-center">
                <a href="{{ route('quran.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Surahs
                </a>
                
                <div class="flex space-x-3">
                    @if($surah->surah_number > 1)
                    <a href="{{ route('quran.show', $surah->surah_number - 1) }}" 
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>
                        Previous Surah
                    </a>
                    @endif
                    
                    @if($surah->surah_number < 114)
                    <a href="{{ route('quran.show', $surah->surah_number + 1) }}" 
                       class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        Next Surah
                        <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function markProgress(verseId, type) {
    fetch(`/quran/verse/${verseId}/${type}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating progress');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating progress');
    });
}
</script>

<style>
.font-arabic {
    font-family: 'Amiri', 'Scheherazade New', 'Times New Roman', serif;
    font-size: 1.5rem;
    line-height: 2;
}
</style>
@endsection