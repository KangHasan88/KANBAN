@extends('layouts.guest')

@section('content')
<div class="min-h-screen grid place-items-center px-4 py-8">
    <div class="auth-card">
        <div class="flex items-center gap-3 mb-6">
            <div class="brand-mark">K</div>
            <div>
                <h1 class="text-lg font-extrabold" style="color: #071a3d;">Kanban</h1>
                <p class="text-sm" style="color: #64748b;">Kelola pekerjaan, alur, dan prioritas tim.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">
                    Username atau Email
                </label>
                <input type="text"
                       name="login"
                       value="{{ old('login') }}"
                       class="form-control"
                       placeholder="john_doe atau john@example.com"
                       required
                       autofocus>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">
                    Password
                </label>
                <input type="password"
                       name="password"
                       class="form-control"
                       required>
            </div>

            <label class="flex items-center gap-2 text-sm font-semibold" style="color: #64748b;">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300" style="accent-color: #123b7a;">
                Ingat saya
            </label>

            <button type="submit" class="auth-btn w-full transition hover:brightness-110">
                Login
            </button>
        </form>

        <div class="mt-6 text-center text-sm" style="color: #64748b;">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-extrabold hover:underline" style="color: #123b7a;">Daftar</a>
        </div>
    </div>
</div>
@endsection
