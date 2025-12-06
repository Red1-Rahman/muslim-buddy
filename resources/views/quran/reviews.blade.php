@extends('layouts.app')

@section('title', 'Quran Reviews - Spaced Repetition')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-emerald-900 mb-2 flex items-center justify-center">
                <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="Quran" class="h-12 w-12 mr-3"> Quran Reviews
            </h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Review memorized verses using spaced repetition to strengthen your memorization
            </p>
        </div>

        <!-- Back to Quran -->
        <div class="mb-6">
            <a href="{{ route('quran.index') }}" 
               class="text-emerald-600 hover:text-emerald-800 flex items-center">
                ← Back to Quran
            </a>
        </div>

        @if($dueVerses->isEmpty())
            <!-- No Reviews Due -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="Quran" class="h-20 w-20">
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">All Caught Up!</h3>
                <p class="text-gray-600 mb-6">
                    You have no verses due for review at this time. Great job keeping up with your memorization!
                </p>
                
                <!-- Stats -->
                <div class="grid md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-emerald-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-emerald-700">{{ $user->total_points ?? 0 }}</div>
                        <div class="text-sm text-emerald-600">Total Points</div>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-700">
                            {{ \App\Models\UserVerseProgress::where('user_id', $user->id)->where('is_memorized', true)->count() }}
                        </div>
                        <div class="text-sm text-blue-600">Memorized Verses</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-700">
                            {{ \App\Models\UserVerseProgress::where('user_id', $user->id)->sum('review_count') }}
                        </div>
                        <div class="text-sm text-purple-600">Total Reviews</div>
                    </div>
                </div>

                <a href="{{ route('quran.index') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                    Continue Reading Quran
                </a>
            </div>
        @else
            <!-- Reviews Available -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $dueVerses->count() }} Verse{{ $dueVerses->count() !== 1 ? 's' : '' }} Due for Review
                    </h2>
                    <div class="text-sm text-gray-600">
                        Spaced Repetition System
                    </div>
                </div>

                <!-- Review Cards -->
                <div class="space-y-6">
                    @foreach($dueVerses as $progress)
                        @php
                            $verse = $progress->verse;
                            $surah = $verse->surah;
                        @endphp
                        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow duration-200">
                            <!-- Verse Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $surah->name_english }} {{ $verse->verse_number }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        Review #{{ $progress->review_count + 1 }}
                                    </span>
                                </div>
                                <div class="text-right text-sm text-gray-500">
                                    Due: {{ $progress->next_review_at->format('M j, g:i A') }}
                                </div>
                            </div>

                            <!-- Arabic Text -->
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg text-right" dir="rtl">
                                <p class="text-2xl leading-relaxed arabic-text text-gray-800">
                                    {{ $verse->arabic_text }}
                                </p>
                            </div>

                            <!-- Translation -->
                            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">English Translation:</h4>
                                <p class="text-blue-900 leading-relaxed">{{ $verse->translation_english }}</p>
                            </div>

                            <!-- Review Actions -->
                            <div class="flex flex-wrap gap-3">
                                <button onclick="reviewVerse({{ $verse->id }}, 'easy')"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 min-w-32">
                                    ✅ Easy (Next: {{ now()->addDays([1, 3, 7, 14, 30, 60][min($progress->review_count, 5)])->format('M j') }})
                                </button>
                                <button onclick="reviewVerse({{ $verse->id }}, 'medium')"
                                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 min-w-32">
                                    ⚠️ Medium (Next: {{ now()->addDays(max(1, [1, 3, 7, 14, 30, 60][min($progress->review_count, 5)] / 2))->format('M j') }})
                                </button>
                                <button onclick="reviewVerse({{ $verse->id }}, 'hard')"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 min-w-32">
                                    ❌ Hard (Next: Tomorrow)
                                </button>
                                <a href="{{ route('quran.show', $surah->surah_number) }}#verse-{{ $verse->verse_number }}"
                                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                    <img src="{{ asset('Quran Circular Cropped.svg') }}" alt="" class="h-4 w-4 inline mr-1"> View in Context
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Study Tips -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="text-blue-400 text-xl">💡</div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Review Tips</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>Easy:</strong> You remembered perfectly - normal interval</li>
                                <li><strong>Medium:</strong> You remembered with some difficulty - shorter interval</li>
                                <li><strong>Hard:</strong> You struggled or forgot - review again tomorrow</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Review Verse Script -->
<script>
async function reviewVerse(verseId, difficulty) {
    try {
        const response = await fetch(`/quran/verse/${verseId}/review`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ difficulty: difficulty })
        });

        if (response.ok) {
            const result = await response.json();
            
            // Show success message
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '✓ Reviewed!';
            button.style.opacity = '0.6';
            button.disabled = true;
            
            // Remove the verse card after a short delay
            setTimeout(() => {
                const card = button.closest('.border');
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '0';
                card.style.transform = 'translateX(100%)';
                
                setTimeout(() => {
                    card.remove();
                    
                    // Check if all cards are removed
                    const remainingCards = document.querySelectorAll('.border.border-gray-200');
                    if (remainingCards.length === 0) {
                        location.reload(); // Refresh to show "All Caught Up" message
                    }
                }, 500);
            }, 1000);

        } else {
            throw new Error('Failed to update review');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update review. Please try again.');
    }
}
</script>
@endsection