@extends('layouts.app')

@section('title', 'Edit Profile - Muslim Buddy')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Profile</h1>
                <p class="text-gray-600">Customize your account and Islamic preferences</p>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Basic Profile Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-user text-emerald-600 mr-2"></i>Basic Information
                    </h2>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   required>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea id="bio" name="bio" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" id="latitude" name="latitude" step="any" value="{{ old('latitude', $user->latitude) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" id="longitude" name="longitude" step="any" value="{{ old('longitude', $user->longitude) }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            </div>
                        </div>

                        <div>
                            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">Location Name</label>
                            <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $user->location_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" 
                                   placeholder="e.g., Dhaka, Bangladesh">
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Islamic Preferences -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-moon text-emerald-600 mr-2"></i>Islamic Preferences
                    </h2>

                    <form action="{{ route('profile.islamic-preferences') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="calculation_method" class="block text-sm font-medium text-gray-700 mb-1">Prayer Calculation Method</label>
                            <select id="calculation_method" name="calculation_method" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Select Method</option>
                                <option value="MuslimWorldLeague" {{ old('calculation_method', $user->calculation_method) == 'MuslimWorldLeague' ? 'selected' : '' }}>Muslim World League</option>
                                <option value="Egyptian" {{ old('calculation_method', $user->calculation_method) == 'Egyptian' ? 'selected' : '' }}>Egyptian General Authority</option>
                                <option value="Karachi" {{ old('calculation_method', $user->calculation_method) == 'Karachi' ? 'selected' : '' }}>University of Karachi</option>
                                <option value="UmmAlQura" {{ old('calculation_method', $user->calculation_method) == 'UmmAlQura' ? 'selected' : '' }}>Umm Al-Qura (Mecca)</option>
                                <option value="Dubai" {{ old('calculation_method', $user->calculation_method) == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                                <option value="MoonsightingCommittee" {{ old('calculation_method', $user->calculation_method) == 'MoonsightingCommittee' ? 'selected' : '' }}>Moonsighting Committee</option>
                                <option value="NorthAmerica" {{ old('calculation_method', $user->calculation_method) == 'NorthAmerica' ? 'selected' : '' }}>ISNA (North America)</option>
                                <option value="Kuwait" {{ old('calculation_method', $user->calculation_method) == 'Kuwait' ? 'selected' : '' }}>Kuwait</option>
                                <option value="Qatar" {{ old('calculation_method', $user->calculation_method) == 'Qatar' ? 'selected' : '' }}>Qatar</option>
                                <option value="Singapore" {{ old('calculation_method', $user->calculation_method) == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Tehran" {{ old('calculation_method', $user->calculation_method) == 'Tehran' ? 'selected' : '' }}>Tehran</option>
                                <option value="Turkey" {{ old('calculation_method', $user->calculation_method) == 'Turkey' ? 'selected' : '' }}>Turkey</option>
                            </select>
                        </div>

                        <div>
                            <label for="madhab" class="block text-sm font-medium text-gray-700 mb-1">Madhab (School of Thought)</label>
                            <select id="madhab" name="madhab" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Select Madhab</option>
                                <option value="Shafi" {{ old('madhab', $user->madhab) == 'Shafi' ? 'selected' : '' }}>Shafi (Standard Asr)</option>
                                <option value="Hanafi" {{ old('madhab', $user->madhab) == 'Hanafi' ? 'selected' : '' }}>Hanafi (Late Asr)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">This affects Asr prayer timing calculation</p>
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                            <select id="timezone" name="timezone" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Auto-detect</option>
                                <option value="Asia/Dhaka" {{ old('timezone', $user->timezone) == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (Bangladesh)</option>
                                <option value="Asia/Riyadh" {{ old('timezone', $user->timezone) == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh (Saudi Arabia)</option>
                                <option value="Europe/London" {{ old('timezone', $user->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London (UK)</option>
                                <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York (USA East)</option>
                                <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles (USA West)</option>
                                <option value="Europe/Istanbul" {{ old('timezone', $user->timezone) == 'Europe/Istanbul' ? 'selected' : '' }}>Europe/Istanbul (Turkey)</option>
                                <option value="Asia/Dubai" {{ old('timezone', $user->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (UAE)</option>
                                <option value="Asia/Karachi" {{ old('timezone', $user->timezone) == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi (Pakistan)</option>
                                <option value="Asia/Jakarta" {{ old('timezone', $user->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (Indonesia)</option>
                                <option value="Asia/Kuala_Lumpur" {{ old('timezone', $user->timezone) == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala_Lumpur (Malaysia)</option>
                                <option value="America/Toronto" {{ old('timezone', $user->timezone) == 'America/Toronto' ? 'selected' : '' }}>America/Toronto (Canada)</option>
                                <option value="Australia/Sydney" {{ old('timezone', $user->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Australia/Sydney</option>
                                <option value="Europe/Paris" {{ old('timezone', $user->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (France)</option>
                                <option value="Europe/Berlin" {{ old('timezone', $user->timezone) == 'Europe/Berlin' ? 'selected' : '' }}>Europe/Berlin (Germany)</option>
                                <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (Japan)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <span id="detected-timezone"></span>
                                Auto-detects your device timezone or select manually.
                            </p>
                        </div>

                        <div class="flex items-center">
                            <input type="hidden" name="prayer_notifications" value="0">
                            <input type="checkbox" id="prayer_notifications" name="prayer_notifications" value="1" 
                                   {{ old('prayer_notifications', $user->prayer_notifications) ? 'checked' : '' }}
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                            <label for="prayer_notifications" class="ml-2 block text-sm text-gray-700">
                                Enable prayer time notifications
                            </label>
                        </div>

                        <div>
                            <label for="reminder_minutes" class="block text-sm font-medium text-gray-700 mb-1">Reminder (minutes before)</label>
                            <select id="reminder_minutes" name="reminder_minutes" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">No reminder</option>
                                <option value="5" {{ old('reminder_minutes', $user->reminder_minutes) == '5' ? 'selected' : '' }}>5 minutes</option>
                                <option value="10" {{ old('reminder_minutes', $user->reminder_minutes) == '10' ? 'selected' : '' }}>10 minutes</option>
                                <option value="15" {{ old('reminder_minutes', $user->reminder_minutes) == '15' ? 'selected' : '' }}>15 minutes</option>
                                <option value="30" {{ old('reminder_minutes', $user->reminder_minutes) == '30' ? 'selected' : '' }}>30 minutes</option>
                            </select>
                        </div>

                        <div>
                            <label for="quran_translation" class="block text-sm font-medium text-gray-700 mb-1">Quran Display Preference</label>
                            <select id="quran_translation" name="quran_translation" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="both" {{ old('quran_translation', $user->quran_translation ?? 'both') == 'both' ? 'selected' : '' }}>Arabic + English</option>
                                <option value="arabic" {{ old('quran_translation', $user->quran_translation) == 'arabic' ? 'selected' : '' }}>Arabic only</option>
                                <option value="english" {{ old('quran_translation', $user->quran_translation) == 'english' ? 'selected' : '' }}>English only</option>
                            </select>
                        </div>

                        <div>
                            <label for="arabic_text_size" class="block text-sm font-medium text-gray-700 mb-1">Arabic Text Size</label>
                            <select id="arabic_text_size" name="arabic_text_size" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="medium" {{ old('arabic_text_size', $user->arabic_text_size ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="small" {{ old('arabic_text_size', $user->arabic_text_size) == 'small' ? 'selected' : '' }}>Small</option>
                                <option value="large" {{ old('arabic_text_size', $user->arabic_text_size) == 'large' ? 'selected' : '' }}>Large</option>
                            </select>
                        </div>

                        <div>
                            <label for="daily_verse_goal" class="block text-sm font-medium text-gray-700 mb-1">Daily Verse Reading Goal</label>
                            <input type="number" id="daily_verse_goal" name="daily_verse_goal" 
                                   value="{{ old('daily_verse_goal', $user->daily_verse_goal ?? 5) }}" 
                                   min="1" max="100"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="hidden" name="enable_night_mode" value="0">
                                <input type="checkbox" id="enable_night_mode" name="enable_night_mode" value="1" 
                                       {{ old('enable_night_mode', $user->enable_night_mode) ? 'checked' : '' }}
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="enable_night_mode" class="ml-2 block text-sm text-gray-700">
                                    Enable night mode for reading
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="auto_mark_prayers" value="0">
                                <input type="checkbox" id="auto_mark_prayers" name="auto_mark_prayers" value="1" 
                                       {{ old('auto_mark_prayers', $user->auto_mark_prayers) ? 'checked' : '' }}
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="auto_mark_prayers" class="ml-2 block text-sm text-gray-700">
                                    Auto-mark prayers as completed
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="hidden" name="congregation_points_bonus" value="0">
                                <input type="checkbox" id="congregation_points_bonus" name="congregation_points_bonus" value="1" 
                                       {{ old('congregation_points_bonus', $user->congregation_points_bonus) ? 'checked' : '' }}
                                       class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                                <label for="congregation_points_bonus" class="ml-2 block text-sm text-gray-700">
                                    Extra points for congregation prayers
                                </label>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                    class="w-full bg-emerald-600 text-white py-2 px-4 rounded-lg hover:bg-emerald-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Update Islamic Preferences
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Update Section -->
            <div class="mt-6 bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-lock text-amber-600 mr-2"></i>Change Password
                </h2>

                <form action="{{ route('profile.password') }}" method="POST" class="max-w-md space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                               required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                               required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500" 
                               required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="bg-amber-600 text-white py-2 px-4 rounded-lg hover:bg-amber-700 transition-colors">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-8">
                <a href="{{ route('profile.show') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-detect user's timezone
document.addEventListener('DOMContentLoaded', function() {
    const timezoneSelect = document.getElementById('timezone');
    const detectedTimezoneSpan = document.getElementById('detected-timezone');
    const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    
    // Show detected timezone
    if (detectedTimezoneSpan) {
        detectedTimezoneSpan.textContent = `Detected: ${userTimezone}. `;
    }
    
    // If no timezone is selected, try to select the detected one
    if (!timezoneSelect.value || timezoneSelect.value === '') {
        for (let option of timezoneSelect.options) {
            if (option.value === userTimezone) {
                option.selected = true;
                break;
            }
        }
    }
});
</script>
@endsection