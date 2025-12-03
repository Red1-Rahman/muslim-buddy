@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">As-salamu alaykum, {{ $user->name }}!</h1>
        <p class="text-gray-600 mt-1">May your day be blessed with faith and productivity.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Points -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <i class="fas fa-coins text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Points</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($user->total_points) }}</p>
                </div>
            </div>
        </div>

        <!-- Prayer Streak -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                    <i class="fas fa-fire text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Prayer Streak</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $user->prayer_streak }} days</p>
                </div>
            </div>
        </div>

        <!-- Weekly Prayers -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-prayer-hands text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $weeklyPrayers }}/35</p>
                </div>
            </div>
        </div>

        <!-- Weekly Verses -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                    <i class="fas fa-book-quran text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Verses Read</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $weeklyVerses }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Prayers -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-mosque text-emerald-600 mr-2"></i>
                    Today's Prayers
                </h2>
                <p class="text-sm text-gray-500 mt-1">Complete all 5 daily prayers</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @php
                        $prayerIcons = [
                            'fajr' => ['icon' => 'sun', 'color' => 'amber', 'time_color' => 'text-amber-600'],
                            'dhuhr' => ['icon' => 'sun', 'color' => 'yellow', 'time_color' => 'text-yellow-600'], 
                            'asr' => ['icon' => 'cloud-sun', 'color' => 'orange', 'time_color' => 'text-orange-600'],
                            'maghrib' => ['icon' => 'moon', 'color' => 'red', 'time_color' => 'text-red-600'],
                            'isha' => ['icon' => 'star', 'color' => 'indigo', 'time_color' => 'text-indigo-600']
                        ];
                    @endphp
                    
                    @foreach($todayPrayers as $prayerName => $prayer)
                    @php
                        $config = $prayerIcons[$prayerName] ?? ['icon' => 'clock', 'color' => 'gray', 'time_color' => 'text-gray-600'];
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg prayer-item-{{ $prayerName }} {{ $prayer['completed'] ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }} transition-all duration-200 hover:shadow-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $prayer['completed'] ? 'bg-green-100' : 'bg-white shadow-sm' }}">
                                @if($prayer['completed'])
                                    <i class="fas fa-check text-green-600 text-lg"></i>
                                @else
                                    <i class="fas fa-{{ $config['icon'] }} {{ $config['time_color'] }}"></i>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="font-semibold text-gray-800 capitalize text-lg">{{ $prayerName }}</p>
                                <div class="flex items-center space-x-3 mt-1">
                                    @if(isset($prayer['time']))
                                        <span class="text-sm {{ $config['time_color'] }} font-medium">{{ $prayer['time'] }}</span>
                                    @endif
                                    @if($prayer['completed'] && $prayer['on_time'])
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-medium">
                                            <i class="fas fa-star mr-1"></i>On Time
                                        </span>
                                    @elseif($prayer['completed'])
                                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium">
                                            <i class="fas fa-check mr-1"></i>Completed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($prayer['completed'])
                                <div class="flex flex-col items-end">
                                    <span class="text-green-600 font-bold text-lg prayer-points-{{ $prayerName }}">+{{ $prayer['points'] }}</span>
                                    <span class="text-xs text-green-600">points</span>
                                </div>
                            @else
                                @php
                                    $prayerLog = \App\Models\PrayerLog::where('user_id', auth()->id())
                                        ->where('prayer_date', now()->toDateString())
                                        ->where('prayer_name', $prayerName)
                                        ->first();
                                @endphp
                                @if($prayerLog)
                                    <button onclick="togglePrayer({{ $prayerLog->id }}, '{{ $prayerName }}')" 
                                            class="prayer-btn-{{ $prayerName }} inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i>
                                        <span class="btn-text-{{ $prayerName }}">Complete</span>
                                    </button>
                                @else
                                    <a href="{{ route('prayers.index') }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i>
                                        Complete
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                
                @php
                    $completedCount = collect($todayPrayers)->where('completed', true)->count();
                    $totalPrayers = count($todayPrayers);
                    $completionRate = $totalPrayers > 0 ? ($completedCount / $totalPrayers) * 100 : 0;
                @endphp
                
                @if($totalPrayers > 0)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Daily Progress</span>
                        <span class="text-sm text-gray-600">{{ $completedCount }}/{{ $totalPrayers }} completed</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full transition-all duration-300" style="width: {{ $completionRate }}%"></div>
                    </div>
                    @if($completedCount === $totalPrayers)
                        <div class="mt-3 text-center">
                            <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                <i class="fas fa-trophy mr-1"></i>
                                All prayers completed! 
                            </span>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Daily Goal -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-bullseye text-indigo-600 mr-2"></i>
                    Today's Goal
                </h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700">Verses Progress</span>
                        <span class="text-gray-600">{{ $todayGoal->verses_completed }}/{{ $todayGoal->target_verses }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $todayGoal->progress_percentage }}%"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-gray-700">All Prayers</span>
                        @if($todayGoal->all_prayers_completed)
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        @else
                        <i class="fas fa-circle text-gray-300 text-xl"></i>
                        @endif
                    </div>
                </div>

                @if($dueReviews > 0)
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        You have <strong>{{ $dueReviews }}</strong> verses due for review
                    </p>
                    <a href="{{ route('quran.reviews') }}" class="text-sm text-yellow-700 font-medium hover:text-yellow-900 mt-2 inline-block">
                        Review now <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('prayers.index') }}" class="bg-gradient-to-r from-green-400 to-green-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-prayer-hands text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Track Prayers</h3>
            <p class="text-sm opacity-90 mt-1">View prayer times & log completion</p>
        </a>

        <a href="{{ route('quran.index') }}" class="bg-gradient-to-r from-indigo-400 to-indigo-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-book-quran text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Read Quran</h3>
            <p class="text-sm opacity-90 mt-1">Track your reading progress</p>
        </a>

        <a href="{{ route('leaderboard.index') }}" class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <i class="fas fa-trophy text-3xl mb-2"></i>
            <h3 class="text-xl font-semibold">Leaderboard</h3>
            <p class="text-sm opacity-90 mt-1">See how you rank globally</p>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize last update timestamp
window.lastDashboardUpdate = Date.now();

function togglePrayer(prayerId, prayerName) {
    const button = document.querySelector(`.prayer-btn-${prayerName}`);
    const btnText = document.querySelector(`.btn-text-${prayerName}`);
    const prayerItem = button.closest('.flex.items-center.justify-between');
    
    // Disable button during request
    button.disabled = true;
    btnText.textContent = 'Loading...';
    
    fetch(`/prayers/${prayerId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update dashboard UI
            updateDashboardPrayerUI(prayerId, prayerName, data.is_completed, data.points, prayerItem);
            
            // Create update object with timestamp
            const prayerUpdate = {
                prayerLogId: prayerId,
                isCompleted: data.is_completed,
                points: data.points,
                prayerName: prayerName,
                timestamp: Date.now(),
                source: 'dashboard'
            };
            
            // Store in localStorage for cross-page sync (temporary)
            localStorage.setItem('prayerUpdate', JSON.stringify(prayerUpdate));
            window.lastDashboardUpdate = prayerUpdate.timestamp;
            
            // Clear localStorage after 3 seconds to prevent stale data
            setTimeout(() => {
                const current = localStorage.getItem('prayerUpdate');
                if (current) {
                    const currentUpdate = JSON.parse(current);
                    if (currentUpdate.timestamp === prayerUpdate.timestamp) {
                        localStorage.removeItem('prayerUpdate');
                    }
                }
            }, 3000);
            
            // Trigger custom event for other tabs/windows
            window.dispatchEvent(new CustomEvent('prayerToggled', {
                detail: prayerUpdate
            }));
            
            // Update progress and stats
            updateProgress();
            updateQuickStats();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (button && btnText) {
            button.disabled = false;
            btnText.textContent = 'Complete';
        }
    });
}

function updateDashboardPrayerUI(prayerId, prayerName, isCompleted, points, prayerItem) {
    const icon = prayerItem.querySelector('.w-12.h-12 i');
    const statusContainer = prayerItem.querySelector('.ml-4 > div:last-child');
    const rightSection = prayerItem.querySelector('.text-right');
    
    // Prayer icons configuration
    const prayerIcons = {
        'fajr': 'sun',
        'dhuhr': 'sun', 
        'asr': 'cloud-sun',
        'maghrib': 'moon',
        'isha': 'star'
    };
    const prayerColors = {
        'fajr': 'text-amber-600',
        'dhuhr': 'text-yellow-600', 
        'asr': 'text-orange-600',
        'maghrib': 'text-red-600',
        'isha': 'text-indigo-600'
    };
    
    if (isCompleted) {
        // Update to completed state
        prayerItem.className = prayerItem.className.replace('bg-gray-50 border-gray-200', 'bg-green-50 border-green-200');
        prayerItem.querySelector('.w-12.h-12').className = 'w-12 h-12 rounded-full flex items-center justify-center bg-green-100';
        icon.className = 'fas fa-check text-green-600 text-lg';
        
        // Add completed badge
        const badge = document.createElement('span');
        badge.className = 'text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-medium';
        badge.innerHTML = '<i class="fas fa-check mr-1"></i>Completed';
        statusContainer.innerHTML = '';
        statusContainer.appendChild(badge);
        
        // Update right section to show points
        rightSection.innerHTML = `
            <div class="flex flex-col items-end">
                <span class="text-green-600 font-bold text-lg prayer-points-${prayerName}">+${points || 0}</span>
                <span class="text-xs text-green-600">points</span>
            </div>
        `;
    } else {
        // Update to incomplete state
        prayerItem.className = prayerItem.className.replace('bg-green-50 border-green-200', 'bg-gray-50 border-gray-200');
        prayerItem.querySelector('.w-12.h-12').className = 'w-12 h-12 rounded-full flex items-center justify-center bg-white shadow-sm';
        
        // Reset icon based on prayer type
        icon.className = `fas fa-${prayerIcons[prayerName]} ${prayerColors[prayerName]}`;
        statusContainer.innerHTML = '';
        
        // Update right section to show button
        rightSection.innerHTML = `
            <button onclick="togglePrayer(${prayerId}, '${prayerName}')" 
                    class="prayer-btn-${prayerName} inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                <i class="fas fa-plus mr-1"></i>
                <span class="btn-text-${prayerName}">Complete</span>
            </button>
        `;
    }
}

function updateProgress() {
    const prayerItems = document.querySelectorAll('.space-y-3 > div');
    let completed = 0;
    let total = 0;
    
    prayerItems.forEach(item => {
        if (item.classList.contains('flex')) {
            total++;
            if (item.classList.contains('bg-green-50')) {
                completed++;
            }
        }
    });
    
    const completionRate = total > 0 ? (completed / total) * 100 : 0;
    
    // Update progress text
    const progressText = document.querySelector('.flex.justify-between.items-center span:last-child');
    if (progressText) {
        progressText.textContent = `${completed}/${total} completed`;
    }
    
    // Update progress bar
    const progressBar = document.querySelector('.bg-gradient-to-r');
    if (progressBar) {
        progressBar.style.width = `${completionRate}%`;
    }
    
    // Show/hide completion message
    const existingMessage = document.querySelector('.mt-3.text-center');
    const progressContainer = document.querySelector('.mt-4.pt-4.border-t');
    
    if (completed === total && total > 0) {
        if (!existingMessage && progressContainer) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mt-3 text-center';
            messageDiv.innerHTML = `
                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                    <i class="fas fa-trophy mr-1"></i>
                    All prayers completed! 
                </span>
            `;
            progressContainer.appendChild(messageDiv);
        }
    } else if (existingMessage && completed < total) {
        existingMessage.remove();
    }
}

function updateQuickStats() {
    // Update the prayer count in quick stats if it exists
    const prayerItems = document.querySelectorAll('.space-y-3 > div');
    let completed = 0;
    
    prayerItems.forEach(item => {
        if (item.classList.contains('flex') && item.classList.contains('bg-green-50')) {
            completed++;
        }
    });
    
    // Find and update the "Today's Prayers" stat if present
    const statCards = document.querySelectorAll('.bg-white.rounded-lg.shadow');
    statCards.forEach(card => {
        const label = card.querySelector('.text-gray-600');
        if (label && label.textContent.includes("Today's Prayers")) {
            const valueElement = card.querySelector('.text-2xl.font-bold');
            if (valueElement) {
                valueElement.textContent = `${completed}/5`;
            }
        }
    });
}

// Listen for storage changes from other tabs (prayers page)
// SINGLE EVENT LISTENER - consolidates both storage events
window.addEventListener('storage', function(e) {
    if (e.key === 'prayerUpdate' && e.newValue) {
        try {
            const prayerUpdate = JSON.parse(e.newValue);
            
            // Only process if newer than last update and has prayer name
            if (prayerUpdate.prayerName && prayerUpdate.timestamp > window.lastDashboardUpdate) {
                const prayerItem = document.querySelector(`.prayer-item-${prayerUpdate.prayerName}`);
                
                if (prayerItem) {
                    updateDashboardPrayerUI(
                        prayerUpdate.prayerLogId, 
                        prayerUpdate.prayerName, 
                        prayerUpdate.isCompleted, 
                        prayerUpdate.points, 
                        prayerItem
                    );
                    updateProgress();
                    updateQuickStats();
                    window.lastDashboardUpdate = prayerUpdate.timestamp;
                }
            }
        } catch (error) {
            console.error('Error processing storage update:', error);
        }
    }
});

// Listen for custom events in same tab
// SINGLE EVENT LISTENER - handles same-tab updates
window.addEventListener('prayerToggled', function(e) {
    const prayerUpdate = e.detail;
    
    // Only process if newer than last update, has prayer name, and not from this page
    if (prayerUpdate.prayerName && 
        prayerUpdate.timestamp > window.lastDashboardUpdate && 
        prayerUpdate.source !== 'dashboard') {
        
        const prayerItem = document.querySelector(`.prayer-item-${prayerUpdate.prayerName}`);
        
        if (prayerItem) {
            updateDashboardPrayerUI(
                prayerUpdate.prayerLogId, 
                prayerUpdate.prayerName, 
                prayerUpdate.isCompleted, 
                prayerUpdate.points, 
                prayerItem
            );
            updateProgress();
            updateQuickStats();
            window.lastDashboardUpdate = prayerUpdate.timestamp;
        }
    }
});

// Check for updates on page load (in case of navigation)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize prayer states based on server data (source of truth)
    const prayerItems = document.querySelectorAll('[class*="prayer-item-"]');
    prayerItems.forEach(item => {
        // Extract prayer name from class
        const prayerName = Array.from(item.classList)
            .find(cls => cls.startsWith('prayer-item-'))
            ?.replace('prayer-item-', '');
        
        if (prayerName) {
            // Check server-provided completion status
            const isCompleted = item.classList.contains('bg-green-50');
            window[`prayer_${prayerName}_completed`] = isCompleted;
        }
    });
    
    // Only apply localStorage updates if they're very recent (within 2 seconds)
    // and different from server state
    const storedUpdate = localStorage.getItem('prayerUpdate');
    if (storedUpdate) {
        try {
            const prayerUpdate = JSON.parse(storedUpdate);
            const isVeryRecent = Date.now() - prayerUpdate.timestamp < 2000;
            
            if (isVeryRecent && prayerUpdate.prayerName) {
                const serverCompleted = window[`prayer_${prayerUpdate.prayerName}_completed`];
                
                // Only apply if different from server state
                if (serverCompleted !== prayerUpdate.isCompleted) {
                    const prayerItem = document.querySelector(`.prayer-item-${prayerUpdate.prayerName}`);
                    if (prayerItem) {
                        updateDashboardPrayerUI(
                            prayerUpdate.prayerLogId,
                            prayerUpdate.prayerName,
                            prayerUpdate.isCompleted,
                            prayerUpdate.points,
                            prayerItem
                        );
                        updateProgress();
                        updateQuickStats();
                    }
                }
            }
        } catch (error) {
            console.error('Error loading stored update:', error);
        }
    }
});
</script>
@endpush
