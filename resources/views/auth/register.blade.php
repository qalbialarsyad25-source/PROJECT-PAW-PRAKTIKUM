@extends('layouts.guest')

@section('title', 'Pendaftaran Petualang')
@section('subtitle', 'Ciptakan legenda barumu di dunia Filkom D&D')

@section('content')
<form action="{{ route('register.submit') }}" method="POST">
    @csrf

    <div class="form-group">
        <label for="name" class="form-label">
            <i class="fa-solid fa-user-shield" style="color: var(--gold-primary); margin-right: 4px;"></i> Nama Lengkap / Julukan
        </label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input" placeholder="Sang Penakluk Bayangan" required autofocus>
        @error('name')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email" class="form-label">
            <i class="fa-solid fa-envelope" style="color: var(--gold-primary); margin-right: 4px;"></i> Alamat Email
        </label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input" placeholder="nama@petualang.com" required>
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">
            <i class="fa-solid fa-lock" style="color: var(--gold-primary); margin-right: 4px;"></i> Kata Sandi (Password)
        </label>
        <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 8 karakter" required>
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">
            <i class="fa-solid fa-shield-check" style="color: var(--gold-primary); margin-right: 4px;"></i> Konfirmasi Kata Sandi
        </label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Ulangi kata sandi" required>
    </div>

    <button type="submit" class="btn-submit">
        <i class="fa-solid fa-feather-pointed"></i> Daftarkan Diri & Mulai
    </button>
</form>

<div class="auth-footer">
    Sudah memiliki akun petualang?
    <a href="{{ route('login') }}">Masuk Sekarang</a>
</div>
@endsection
