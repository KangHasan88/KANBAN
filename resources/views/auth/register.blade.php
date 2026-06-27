@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-96 auth-card">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-3">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg" style="background-color: #1e3a5f;">
                    🎯
                </div>
            </div>
            <h1 class="text-2xl font-bold" style="color: #1e3a5f;">Daftar Akun</h1>
            <p class="text-gray-500 text-sm mt-1">Buat akun baru</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Username *
                </label>
                <input type="text" 
                       name="username" 
                       value="{{ old('username') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
                <p class="text-xs text-gray-400 mt-1">Hanya huruf, angka, dash (-), dan underscore (_)</p>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Nama Lengkap *
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Email *
                </label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
            </div>

            <div class="mb-3">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Password *
                </label>
                <input type="password" 
                       name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
                <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter</p>
            </div>

            <div class="mb-5">
                <label class="block text-gray-700 text-sm font-semibold mb-1">
                    Konfirmasi Password *
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 transition"
                       style="focus:ring-color: #10b981; focus:border-color: #10b981;"
                       required>
            </div>

            <button type="submit" 
                    class="w-full py-2 rounded-xl transition transform hover:scale-[1.02] font-semibold text-white"
                    style="background-color: #10b981;">
                Daftar
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: #1e3a5f;">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection