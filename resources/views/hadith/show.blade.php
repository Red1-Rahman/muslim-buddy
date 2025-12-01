@extends('layouts.app')

@section('title', 'Hadith ' . $hadith->formatted_number . ' - ' . $hadith->collection->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Navigation -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('hadith.index', ['collection' => $hadith->collection_id, 'chapter' => $hadith->chapter_id]) }}" 
               class="text-emerald-600 hover:text-emerald-800 flex items-center">
                ← Back to {{ $hadith->chapter->chapter_name_english }}
            </a>
            
            <!-- Navigation between hadiths -->
            <div class="flex space-x-2">
                @if($previousHadith)
                    <a href="{{ route('hadith.show', $previousHadith) }}" 
                       class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
                        ← Previous
                    </a>
                @endif
                @if($nextHadith)
                    <a href="{{ route('hadith.show', $nextHadith) }}" 
                       class="px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
                        Next →
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Hadith {{ $hadith->formatted_number }}</h1>
                        <div class="space-y-1 text-emerald-100">
                            <p>{{ $hadith->collection->name }} • Chapter {{ number_format($hadith->chapter->chapter_number, 0) }}</p>
                            <p>{{ $hadith->chapter->chapter_name_english }}</p>
                        </div>
                    </div>
                    
                    <!-- Grade Badge -->
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 bg-white text-gray-800 rounded-full text-sm font-medium">
                            {{ $hadith->english_grade }}
                        </span>
                        @if(!$hadith->collection->is_verified)
                            <div class="mt-2 text-xs text-emerald-100">
                                ⚠️ Auto-annotated collection
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Warning for auto-annotated collections -->
            @if($hadith->collection->warning_message)
                <div class="bg-yellow-50 border-l-4 border-yellow-300 p-4">
                    <div class="flex items-center">
                        <div class="text-yellow-400 text-lg mr-3">⚠️</div>
                        <p class="text-yellow-700 text-sm">{{ $hadith->collection->warning_message }}</p>
                    </div>
                </div>
            @endif

            <!-- Content -->
            <div class="p-6 space-y-8">
                <!-- Section Info -->
                @if($hadith->section)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Section {{ number_format($hadith->section->section_number, 0) }}</h3>
                        <p class="text-gray-700">{{ $hadith->section->section_name_english }}</p>
                        @if($hadith->section->section_name_arabic)
                            <p class="text-gray-600 text-right mt-1 arabic-text" dir="rtl">{{ $hadith->section->section_name_arabic }}</p>
                        @endif
                    </div>
                @endif

                <!-- English Version -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">English Translation</h2>
                    
                    <!-- Isnad (Chain of Narrators) -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">Chain of Narrators (Isnad):</h3>
                        <p class="text-blue-900 text-sm leading-relaxed">{{ $hadith->english_isnad }}</p>
                    </div>
                    
                    <!-- Matn (Main Text) -->
                    <div class="prose max-w-none">
                        <p class="text-gray-800 leading-relaxed text-lg">{{ $hadith->english_matn }}</p>
                    </div>
                </div>

                <!-- Arabic Version -->
                <div class="space-y-4">
                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Arabic Original</h2>
                    
                    <!-- Arabic Isnad -->
                    <div class="bg-amber-50 rounded-lg p-4 text-right" dir="rtl">
                        <h3 class="text-sm font-semibold text-amber-800 mb-2">السند:</h3>
                        <p class="text-amber-900 text-sm leading-relaxed arabic-text">{{ $hadith->arabic_isnad }}</p>
                    </div>
                    
                    <!-- Arabic Matn -->
                    <div class="text-right" dir="rtl">
                        <p class="text-gray-800 leading-relaxed text-lg arabic-text">{{ $hadith->arabic_matn }}</p>
                    </div>

                    <!-- Arabic Comment if available -->
                    @if($hadith->arabic_comment)
                        <div class="bg-gray-50 rounded-lg p-4 text-right" dir="rtl">
                            <h3 class="text-sm font-semibold text-gray-800 mb-2">تعليق:</h3>
                            <p class="text-gray-700 text-sm arabic-text">{{ $hadith->arabic_comment }}</p>
                        </div>
                    @endif
                </div>

                <!-- User Progress Section -->
                @auth
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Progress</h3>
                        <div class="space-y-4">
                            <!-- Progress Status -->
                            <div class="flex items-center space-x-4">
                                <label class="text-sm font-medium text-gray-700">Status:</label>
                                <select id="progress-status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                                    <option value="not_read" {{ ($userProgress->status ?? 'not_read') == 'not_read' ? 'selected' : '' }}>Not Read</option>
                                    <option value="read" {{ ($userProgress->status ?? 'not_read') == 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="memorized" {{ ($userProgress->status ?? 'not_read') == 'memorized' ? 'selected' : '' }}>Memorized</option>
                                </select>
                            </div>

                            <!-- Progress Times -->
                            @if($userProgress)
                                <div class="text-sm text-gray-600 space-y-1">
                                    @if($userProgress->read_at)
                                        <p>Read: {{ $userProgress->read_at->format('M j, Y \a\t g:i A') }}</p>
                                    @endif
                                    @if($userProgress->memorized_at)
                                        <p>Memorized: {{ $userProgress->memorized_at->format('M j, Y \a\t g:i A') }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes:</label>
                                <textarea id="progress-notes" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500"
                                          placeholder="Add your personal notes about this hadith...">{{ $userProgress->notes ?? '' }}</textarea>
                            </div>

                            <!-- Save Button -->
                            <button id="save-progress" 
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500">
                                Save Progress
                            </button>
                        </div>
                    </div>
                @else
                    <div class="border-t pt-6">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-blue-800 mb-2">Track your reading progress</p>
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Sign in to save your progress
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        <!-- Chapter Navigation -->
        <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">More from {{ $hadith->chapter->chapter_name_english }}</h3>
            <div class="flex space-x-4">
                @if($previousHadith)
                    <a href="{{ route('hadith.show', $previousHadith) }}" 
                       class="flex-1 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="text-sm text-gray-600">← Previous</div>
                        <div class="font-medium">Hadith {{ $previousHadith->formatted_number }}</div>
                        <div class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($previousHadith->english_matn, 100) }}</div>
                    </a>
                @endif
                
                @if($nextHadith)
                    <a href="{{ route('hadith.show', $nextHadith) }}" 
                       class="flex-1 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-right">
                        <div class="text-sm text-gray-600">Next →</div>
                        <div class="font-medium">Hadith {{ $nextHadith->formatted_number }}</div>
                        <div class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($nextHadith->english_matn, 100) }}</div>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@auth
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('progress-status');
    const notesTextarea = document.getElementById('progress-notes');
    const saveButton = document.getElementById('save-progress');
    
    saveButton.addEventListener('click', async function() {
        const status = statusSelect.value;
        const notes = notesTextarea.value;
        
        try {
            const response = await fetch(`/hadith/{{ $hadith->id }}/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status, notes })
            });
            
            if (response.ok) {
                saveButton.textContent = 'Saved!';
                saveButton.style.backgroundColor = '#10B981';
                
                setTimeout(() => {
                    saveButton.textContent = 'Save Progress';
                    saveButton.style.backgroundColor = '';
                }, 2000);
            }
        } catch (error) {
            console.error('Error saving progress:', error);
            saveButton.textContent = 'Error!';
            saveButton.style.backgroundColor = '#EF4444';
            
            setTimeout(() => {
                saveButton.textContent = 'Save Progress';
                saveButton.style.backgroundColor = '';
            }, 2000);
        }
    });
});
</script>
@endauth
@endsection