<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Muslim Buddy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <div class="flex items-center justify-center min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center mb-4">
                    <img src="{{ asset('Muslim Buddy.svg') }}" alt="Muslim Buddy Logo" class="h-16 w-16">
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Forgot your password?
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your email address and we'll send you a link to reset your password.
                </p>
            </div>

            <div class="bg-white py-8 px-6 shadow rounded-lg">
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border @error('email') border-red-300 @else border-gray-300 @enderror placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm"
                               placeholder="Enter your email address">
                        
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Send Password Reset Link
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-sm text-green-600 hover:text-green-500">
                            Back to login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>