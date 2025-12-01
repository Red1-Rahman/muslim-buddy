@extends('layouts.app')

@section('title', 'Prayer Times')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-prayer-hands text-indigo-600"></i> Prayer Times
        </h1>
        <p class="text-gray-600 mt-1 prayer-date">{{ $date->format('l, F j, Y') }}</p>
        @if($user->location_name)
        <p class="text-gray-500 text-sm mt-1">
            <i class="fas fa-map-marker-alt"></i> {{ $user->location_name }}
        </p>
        @endif
    </div>

    <!-- Prayer Times Display -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold">Today's Prayer Schedule</h2>
                    <p class="text-indigo-100 mt-1">Method: {{ $user->calculation_method ?? 'Muslim World League' }} | Madhab: {{ $user->madhab ?? 'Shafi' }}</p>
                </div>
                <a href="{{ route('prayers.test') }}" 
                   class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-flask mr-2"></i>Test Accuracy
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach(['fajr' => $prayerTimes->fajr, 'dhuhr' => $prayerTimes->dhuhr, 'asr' => $prayerTimes->asr, 'maghrib' => $prayerTimes->maghrib, 'isha' => $prayerTimes->isha] as $name => $time)
                <div class="text-center p-4 rounded-lg {{ $prayerLogs[$name]->is_completed ? 'bg-green-50 border-2 border-green-500' : 'bg-gray-50' }}">
                    <h3 class="text-lg font-semibold text-gray-900 capitalize mb-2">{{ $name }}</h3>
                    <p class="text-3xl font-bold text-indigo-600 mb-3">{{ $time->format('h:i A') }}</p>
                    
                    <button 
                        onclick="togglePrayer({{ $prayerLogs[$name]->id }})"
                        class="w-full px-4 py-2 rounded-md text-sm font-medium transition {{ $prayerLogs[$name]->is_completed ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                        @if($prayerLogs[$name]->is_completed)
                        <i class="fas fa-check-circle mr-1"></i> Completed
                        @else
                        <i class="fas fa-circle mr-1"></i> Mark Done
                        @endif
                    </button>
                    
                    @if($prayerLogs[$name]->is_completed)
                    <p class="text-xs text-green-600 mt-2">+{{ $prayerLogs[$name]->points_earned }} points</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Additional Times -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Sunrise and Sunset -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-sun text-yellow-500 mr-2"></i> Sunrise & Sunset
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Sunrise</span>
                    <span class="font-semibold text-gray-900">{{ $prayerTimes->sunrise->format('h:i A') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Sunset</span>
                    <span class="font-semibold text-gray-900">{{ $prayerTimes->sunset->format('h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Forbidden Times -->
        <div class="bg-red-50 rounded-lg shadow p-6 border border-red-200">
            <h3 class="text-lg font-semibold text-red-900 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i> Forbidden Prayer Times
            </h3>
            <div class="space-y-2 text-sm">
                <p class="text-red-800">
                    <strong>After Fajr:</strong> {{ $prayerTimes->fajr->format('h:i A') }} - {{ $prayerTimes->forbiddenAfterFajr->format('h:i A') }}
                </p>
                <p class="text-red-800">
                    <strong>Before Dhuhr:</strong> {{ $prayerTimes->forbiddenBeforeDhuhr->format('h:i A') }} - {{ $prayerTimes->dhuhr->format('h:i A') }}
                </p>
                <p class="text-red-800">
                    <strong>After Asr:</strong> {{ $prayerTimes->asr->format('h:i A') }} - {{ $prayerTimes->sunset->format('h:i A') }}
                </p>
            </div>
            @if($prayerTimes->isForbiddenTime())
            <div class="mt-4 bg-red-100 border border-red-300 rounded p-3">
                <p class="text-red-900 font-medium text-sm">
                    <i class="fas fa-ban mr-1"></i> It is currently forbidden prayer time
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- Current Prayer -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white mb-8">
        <div class="text-center">
            <p class="text-lg opacity-90">Current Prayer</p>
            <h2 class="text-4xl font-bold capitalize mt-2">{{ $prayerTimes->currentPrayer() ?? 'None' }}</h2>
            <p class="text-lg opacity-90 mt-4">Next Prayer: <span class="font-semibold capitalize">{{ $prayerTimes->nextPrayer() }}</span></p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <i class="fas fa-fire text-orange-500 text-3xl mb-2"></i>
            <p class="text-2xl font-bold text-gray-900">{{ $user->prayer_streak }}</p>
            <p class="text-gray-600">Day Streak</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
            <p class="text-2xl font-bold text-gray-900">{{ count(array_filter($prayerLogs, fn($log) => $log->is_completed)) }}/5</p>
            <p class="text-gray-600">Today's Prayers</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <i class="fas fa-chart-line text-indigo-500 text-3xl mb-2"></i>
            <a href="{{ route('prayers.statistics') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                View Statistics <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-detect and send timezone with requests
document.addEventListener('DOMContentLoaded', function() {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Set timezone header for all AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        options.headers = {
            ...options.headers,
            'X-User-Timezone': userTimezone
        };
        return originalFetch(url, options);
    };
    
    // Update current page with correct timezone if needed
    const currentDate = new Date().toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric', 
        month: 'long',
        day: 'numeric',
        timeZone: userTimezone
    });
    
    const dateElement = document.querySelector('.prayer-date');
    if (dateElement) {
        dateElement.textContent = currentDate;
    }
});

function togglePrayer(prayerLogId) {
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    fetch(`/prayers/${prayerLogId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-User-Timezone': userTimezone
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection
