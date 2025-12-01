@extends('layouts.app')

@section('title', 'Hadiths - Islamic Guidance')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-emerald-900 mb-2">📜 Hadith Collections</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Study authentic sayings and teachings of Prophet Muhammad (ﷺ) from verified hadith collections
            </p>
        </div>

        <!-- Collections Grid -->
        @if($collections->isEmpty())
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📚</div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Hadith Collections Available</h3>
                <p class="text-gray-500">Hadith collections are being prepared.</p>
            </div>
        @else
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                @foreach($collections as $collection)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                        <div class="p-6">
                            <!-- Collection Header -->
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold text-gray-800">{{ $collection->name }}</h3>
                                @if($collection->is_verified)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        ✓ Verified
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Auto-annotated
                                    </span>
                                @endif
                            </div>

                            <p class="text-gray-600 text-sm mb-3">{{ $collection->name_arabic }}</p>

                            <!-- Warning for auto-annotated collections -->
                            @if(!$collection->is_verified)
                                <div class="bg-yellow-50 border-l-4 border-yellow-300 p-3 mb-4">
                                    <div class="flex items-start">
                                        <div class="text-yellow-400 mr-2">⚠️</div>
                                        <div class="text-xs text-yellow-700">
                                            This collection is auto-annotated with {{ $collection->accuracy_percentage }}% accuracy. 
                                            Please verify important hadiths from scholarly sources.
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="flex justify-between text-sm text-gray-500 mb-4">
                                <span>{{ $collection->chapters_count ?? 0 }} Chapters</span>
                                <span>{{ $collection->hadiths_count ?? 0 }} Hadiths</span>
                            </div>

                            <!-- Browse Button -->
                            <a href="{{ route('hadith.index', ['collection' => $collection->id]) }}" 
                               class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-center block">
                                Browse Collection
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Selected Collection Content -->
        @if($selectedCollection)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $selectedCollection->name }}</h2>
                        <p class="text-gray-600">{{ $selectedCollection->name_arabic }}</p>
                    </div>
                    <a href="{{ route('hadith.index') }}" class="text-emerald-600 hover:text-emerald-800">
                        ← Back to Collections
                    </a>
                </div>

                <!-- Warning Banner for auto-annotated -->
                @if($selectedCollection->warning_message)
                    <div class="bg-yellow-50 border-l-4 border-yellow-300 p-4 mb-6">
                        <div class="flex items-center">
                            <div class="text-yellow-400 text-lg mr-3">⚠️</div>
                            <p class="text-yellow-700 text-sm">{{ $selectedCollection->warning_message }}</p>
                        </div>
                    </div>
                @endif

                <!-- Chapters List -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($chapters as $chapter)
                        <a href="{{ route('hadith.index', ['collection' => $selectedCollection->id, 'chapter' => $chapter->id]) }}"
                           class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-medium text-emerald-600">Chapter {{ number_format($chapter->chapter_number, 0) }}</span>
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                    {{ $chapter->hadiths_count ?? 0 }} hadiths
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2">{{ $chapter->chapter_name_english }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $chapter->chapter_name_arabic }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Hadiths List -->
        @if($hadiths->isNotEmpty())
            <div class="bg-white rounded-xl shadow-lg p-6">
                <!-- Filters -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4 w-full">
                        <input type="hidden" name="collection" value="{{ request('collection') }}">
                        <input type="hidden" name="chapter" value="{{ request('chapter') }}">
                        
                        <!-- Search -->
                        <div class="flex-1 min-w-64">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search hadiths..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        
                        <!-- Grade Filter -->
                        <select name="grade" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                            <option value="">All Grades</option>
                            <option value="Sahih" {{ request('grade') == 'Sahih' ? 'selected' : '' }}>Sahih (Authentic)</option>
                            <option value="Hasan" {{ request('grade') == 'Hasan' ? 'selected' : '' }}>Hasan (Good)</option>
                            <option value="Daif" {{ request('grade') == 'Daif' ? 'selected' : '' }}>Daif (Weak)</option>
                        </select>
                        
                        <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Hadiths List -->
                <div class="space-y-6">
                    @foreach($hadiths as $hadith)
                        <div class="border-l-4 border-emerald-500 pl-4 py-4 hover:bg-gray-50 rounded-r-lg transition-colors duration-200">
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center space-x-3">
                                    <span class="font-bold text-emerald-700">Hadith {{ $hadith->formatted_number }}</span>
                                    <span class="text-sm px-2 py-1 rounded-full {{ $hadith->grade_color }} bg-opacity-10">
                                        {{ $hadith->english_grade }}
                                    </span>
                                </div>
                                @auth
                                    <div class="flex items-center space-x-2">
                                        <select class="hadith-progress text-xs border border-gray-300 rounded px-2 py-1" 
                                                data-hadith-id="{{ $hadith->id }}">
                                            <option value="not_read" {{ ($hadith->user_progress_status ?? 'not_read') == 'not_read' ? 'selected' : '' }}>Not Read</option>
                                            <option value="read" {{ ($hadith->user_progress_status ?? 'not_read') == 'read' ? 'selected' : '' }}>Read</option>
                                            <option value="memorized" {{ ($hadith->user_progress_status ?? 'not_read') == 'memorized' ? 'selected' : '' }}>Memorized</option>
                                        </select>
                                    </div>
                                @endauth
                            </div>

                            <!-- Content Preview -->
                            <div class="space-y-3">
                                <!-- English -->
                                <div>
                                    <p class="text-gray-800 line-clamp-3">{{ Str::limit($hadith->english_matn, 300) }}</p>
                                </div>
                                
                                <!-- Arabic -->
                                <div class="text-right" dir="rtl">
                                    <p class="text-gray-700 arabic-text line-clamp-2">{{ Str::limit($hadith->arabic_matn, 200) }}</p>
                                </div>
                            </div>

                            <!-- Read More Link -->
                            <div class="mt-3">
                                <a href="{{ route('hadith.show', $hadith) }}" 
                                   class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                    Read Full Hadith →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($hadiths->hasPages())
                    <div class="mt-6">
                        {{ $hadiths->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle progress updates
    const progressSelects = document.querySelectorAll('.hadith-progress');
    
    progressSelects.forEach(select => {
        select.addEventListener('change', async function() {
            const hadithId = this.dataset.hadithId;
            const status = this.value;
            
            try {
                const response = await fetch(`/hadith/${hadithId}/progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status })
                });
                
                if (response.ok) {
                    // Show success feedback
                    this.style.borderColor = '#10B981';
                    setTimeout(() => {
                        this.style.borderColor = '#D1D5DB';
                    }, 1000);
                }
            } catch (error) {
                console.error('Error updating progress:', error);
                // Reset select on error
                this.value = 'not_read';
            }
        });
    });
});
</script>
@endauth
@endsection