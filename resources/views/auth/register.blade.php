@extends('layouts.guest')

@section('content')
<div class="min-h-screen grid place-items-center px-4 py-8">
    <div class="auth-card">
        <div class="mb-6 flex items-center gap-3">
            <div class="brand-mark">K</div>
            <div>
                <h1 class="text-lg font-extrabold" style="color: #071a3d;">Daftar Akun</h1>
                <p class="text-sm" style="color: #64748b;">Buat akses untuk mulai mengelola board.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-3">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">Username *</label>
                <input type="text" name="username" value="{{ old('username') }}" class="form-control" required>
                <p class="mt-1 text-xs" style="color: #64748b;">Huruf, angka, dash, dan underscore.</p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">Nama Lengkap *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">Password *</label>
                <input type="password" name="password" class="form-control" required>
                <p class="mt-1 text-xs" style="color: #64748b;">Minimal 8 karakter.</p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-bold" style="color: #334155;">Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="auth-btn w-full transition hover:brightness-110">
                Daftar
            </button>
        </form>

        <div class="mt-6 text-center text-sm" style="color: #64748b;">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-extrabold hover:underline" style="color: #123b7a;">Login</a>
        </div>
    </div>
</div>
@endsection
