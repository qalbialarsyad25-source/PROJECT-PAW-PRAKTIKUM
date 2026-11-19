@extends('layouts.guest')

@section('title', 'Masuk Petualang')
@section('subtitle', 'Lanjutkan kisah perjalanan RPG Anda')

@section('content')
<form action="{{ route('login.submit') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">
            <i class="fa-solid fa-envelope" style="color: var(--gold-primary); margin-right: 4px;"></i> Alamat Email
        </label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="nama@petualang.com" required autofocus>
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            <i class="fa-solid fa-lock" style="color: var(--gold-primary); margin-right: 4px;"></i> Kata Sandi (Password)
        </label>
        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 0.85rem;">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #94a3b8;">
            <input type="checkbox" name="remember" style="accent-color: var(--gold-bright);">
            <span>Ingat Sesi Saya</span>
        </label>
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-dungeon"></i> Masuki Dunia RPG
    </button>

    <!-- Quick Demo Account Button -->
    <div style="margin-top: 14px; text-align: center;">
        <button type="button" id="fillDemoBtn" style="background: rgba(212, 175, 55, 0.1); border: 1px dashed rgba(212, 175, 55, 0.4); color: var(--gold-bright); font-size: 0.8rem; padding: 6px 14px; border-radius: 6px; cursor: pointer; width: 100%;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Gunakan Akun Demo (player@dnd.test)
        </button>
    </div>
</form>

<div class="auth-footer">
    Belum memiliki karakter dan akun?
    <a href="{{ route('register') }}">Daftar Petualang Baru</a>
</div>

<script>
    document.getElementById('fillDemoBtn').addEventListener('click', function() {
        document.getElementById('email').value = 'player@dnd.test';
        document.getElementById('password').value = 'password123';
    });
</script>
@endsection
