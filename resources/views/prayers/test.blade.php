@extends('layouts.app')

@section('title', 'Prayer Times Test - Muslim Buddy')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-emerald-50 to-blue-50">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Prayer Times Accuracy Test</h1>
                <p class="text-gray-600">Comparing calculations for major cities worldwide</p>
            </div>

            <!-- Cities Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testResults as $city)
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $city['name'] }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $city['coordinates'] }}</p>
                    <p class="text-sm text-gray-500 mb-4">Method: {{ $city['method'] }}</p>
                    
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-amber-700">Fajr</span>
                            <span class="text-gray-800">{{ $city['times']['fajr'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-yellow-600">Sunrise</span>
                            <span class="text-gray-800">{{ $city['times']['sunrise'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-blue-600">Dhuhr</span>
                            <span class="text-gray-800">{{ $city['times']['dhuhr'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-orange-600">Asr</span>
                            <span class="text-gray-800">{{ $city['times']['asr'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-red-600">Maghrib</span>
                            <span class="text-gray-800">{{ $city['times']['maghrib'] }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-purple-700">Isha</span>
                            <span class="text-gray-800">{{ $city['times']['isha'] }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            Current: {{ $city['current_prayer'] ?? 'N/A' }}
                            @if($city['is_forbidden_time'])
                                | <span class="text-red-600">Forbidden Time</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Calculation Details -->
            <div class="mt-12 bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Calculation Notes</h2>
                <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-600">
                    <div>
                        <h3 class="font-semibold mb-2">Improvements Made:</h3>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>Proper timezone offset calculations</li>
                            <li>Accurate solar coordinate computations</li>
                            <li>Correct apparent sidereal time formula</li>
                            <li>Standard geometric altitude for sunrise/sunset (-0.833°)</li>
                            <li>Proper method-specific adjustments</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-2">Based on TypeScript Implementation:</h3>
                        <ul class="space-y-1 list-disc list-inside">
                            <li>SolarCoordinates class improvements</li>
                            <li>Astronomical calculation precision</li>
                            <li>CalculationMethod parameters</li>
                            <li>Time component handling</li>
                            <li>Nutation and obliquity corrections</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-8">
                <a href="{{ route('prayers.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Prayer Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection