@extends('layouts.app')

@section('title', 'Profile - Muslim Buddy')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ $errors->first() }}
            </div>
            @endif

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-24 h-24 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Avatar" class="w-full h-full rounded-full object-cover">
                    @else
                        <span class="text-white text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $user->name }}</h1>
                <p class="text-gray-600">{{ $user->email }}</p>
                @if($user->location_name)
                    <p class="text-gray-500 text-sm mt-1">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ $user->location_name }}
                    </p>
                @endif
            </div>

            <!-- Quran.Foundation Integration -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-emerald-100">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            <i class="fas fa-link text-emerald-600 mr-2"></i>Quran.Foundation Account
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">OAuth2 + User Profile API integration for hackathon judging.</p>
                    </div>
                    @if($hasQfLink)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                        Linked
                    </span>
                    @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                        Not Linked
                    </span>
                    @endif
                </div>

                <div class="grid md:grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-500">QF User ID</p>
                        <p class="font-medium text-gray-800">{{ $user->qf_user_id ?: 'Not available yet' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">QF Email</p>
                        <p class="font-medium text-gray-800">{{ $user->qf_email ?: 'Not available yet' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Last Profile Sync</p>
                        <p class="font-medium text-gray-800">
                            {{ $user->qf_profile_synced_at ? $user->qf_profile_synced_at->diffForHumans() : 'Never synced' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500">Session Token</p>
                        <p class="font-medium {{ $hasQfSessionToken ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $hasQfSessionToken ? 'Active in this session' : 'Not active in this session' }}
                        </p>
                    </div>
                </div>

                @if(is_array($qfProfile) && !empty($qfProfile))
                <div class="bg-gray-50 rounded-lg p-4 mb-4 text-sm">
                    <p class="font-medium text-gray-700 mb-2">Latest Quran.Foundation User Data</p>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <p class="text-gray-500">Name</p>
                            <p class="text-gray-800">
                                {{ trim((string)($qfProfile['firstName'] ?? $qfProfile['first_name'] ?? '') . ' ' . (string)($qfProfile['lastName'] ?? $qfProfile['last_name'] ?? '')) ?: ($qfProfile['name'] ?? 'N/A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="text-gray-800">{{ $qfProfile['email'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('auth.qf.redirect') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                        <i class="fas fa-right-to-bracket mr-2"></i>{{ $hasQfLink ? 'Reconnect Quran.Foundation' : 'Connect Quran.Foundation' }}
                    </a>

                    @if($hasQfSessionToken)
                    <form method="POST" action="{{ route('auth.qf.sync') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-rotate mr-2"></i>Sync User Profile
                        </button>
                    </form>

                    <form method="POST" action="{{ route('auth.qf.disconnect') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-unlink mr-2"></i>Disconnect Session
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-3xl font-bold text-emerald-600 mb-2">{{ $stats['total_points'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Total Points</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['prayer_streak'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Prayer Streak</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $stats['verses_read'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Verses Read</div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6 text-center">
                    <div class="text-3xl font-bold text-amber-600 mb-2">{{ $stats['verses_memorized'] ?? 0 }}</div>
                    <div class="text-gray-600 text-sm">Verses Memorized</div>
                </div>
            </div>

            <!-- Progress Cards -->
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Quran Progress -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-book-open text-emerald-600 mr-2"></i>Quran Progress
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Reading Progress</span>
                                <span>{{ $stats['quran_read_percentage'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $stats['quran_read_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Memorization Progress</span>
                                <span>{{ $stats['quran_memorized_percentage'] ?? 0 }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['quran_memorized_percentage'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Goal -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-target text-blue-600 mr-2"></i>Today's Goal
                    </h3>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Verses Goal</span>
                            <span>{{ $todayGoal->verses_completed ?? 0 }} / {{ $todayGoal->target_verses ?? 5 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ $todayGoal && $todayGoal->target_verses > 0 ? min(100, ($todayGoal->verses_completed / $todayGoal->target_verses) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @if($todayGoal && $todayGoal->all_prayers_completed)
                        <div class="flex items-center text-emerald-600 text-sm">
                            <i class="fas fa-check-circle mr-2"></i>All prayers completed today!
                        </div>
                    @else
                        <div class="flex items-center text-amber-600 text-sm">
                            <i class="fas fa-clock mr-2"></i>Prayer goal in progress
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            @if($recentVerses && $recentVerses->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-history text-gray-600 mr-2"></i>Recent Reading Activity
                </h3>
                <div class="space-y-3">
                    @foreach($recentVerses as $verseProgress)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <span class="font-medium text-gray-800">
                                    {{ $verseProgress->verse->surah->name_english ?? 'Unknown Surah' }}
                                </span>
                                <span class="text-gray-600 text-sm">
                                    - Verse {{ $verseProgress->verse->verse_number ?? 'Unknown' }}
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">
                                {{ $verseProgress->read_at ? $verseProgress->read_at->diffForHumans() : 'Recently' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('profile.edit') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
                <a href="{{ route('prayers.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-prayer-hands mr-2"></i>Prayer Times
                </a>
                <a href="{{ route('quran.index') }}" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-book mr-2"></i>Continue Reading
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
