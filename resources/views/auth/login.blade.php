@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96 auth-card">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-3">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg" style="background-color: #1e3a5f;">
                    🎯
                </div>
            </div>
            <h1 class="text-2xl font-bold" style="color: #1e3a5f;">Kanban Board</h1>
            <p class="text-gray-500 text-sm mt-1">Login dengan username atau email</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Username atau Email
                </label>
                <input type="text" 
                       name="login" 
                       value="{{ old('login') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required 
                       autofocus>
                <p class="text-xs text-gray-400 mt-1">Contoh: john_doe atau john@example.com</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">
                    Password
                </label>
                <input type="password" 
                       name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
            </div>

            <div class="mb-6 flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="mr-2 w-4 h-4" style="accent-color: #10b981;">
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-2 rounded-xl transition transform hover:scale-[1.02] font-semibold text-white"
                    style="background-color: #10b981;">
                Login
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold hover:underline" style="color: #1e3a5f;">Daftar</a>
            </p>
        </div>
    </div>
</div>
@endsection