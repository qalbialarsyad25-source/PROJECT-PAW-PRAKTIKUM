@extends('layouts.app')

@section('title', 'Profil Petualang')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 28px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 18px;">
        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-scroll" style="color: var(--gold-bright);"></i>
            <span>Gulungan Profil Petualang</span>
        </h1>
        <p style="color: #94a3b8; font-size: 0.92rem; margin-top: 4px;">
            Kelola identitas, alamat email, dan kredensial keamanan akun permainan Anda.
        </p>
    </div>

    <div class="rpg-card" style="padding: 30px;">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-id-card"></i> Identitas Akun
            </h3>

            <div class="form-group">
                <label for="name" class="form-label">Nama Lengkap / Julukan</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <hr style="border: 0; border-top: 1px solid #242938; margin: 30px 0;">

            <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-key"></i> Perbarui Kata Sandi (Opsional)
            </h3>
            <p style="color: #64748b; font-size: 0.82rem; margin-bottom: 18px;">
                Biarkan kolom di bawah ini kosong jika Anda tidak ingin mengubah kata sandi Anda.
            </p>

            <div class="form-group">
                <label for="current_password" class="form-label">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" name="current_password" class="form-input" placeholder="Masukkan kata sandi lama">
                @error('current_password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="new_password" class="form-label">Kata Sandi Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-input" placeholder="Minimal 8 karakter">
                    @error('new_password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input" placeholder="Ulangi kata sandi baru">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn-rpg btn-rpg-gold">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
