@extends('layouts.app')

@section('title', 'Edit Karakter - ' . $character->name)

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div style="margin-bottom: 24px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 class="font-display" style="font-size: 1.6rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-pen-nib" style="color: var(--gold-bright);"></i>
                <span>Edit Catatan Karakter</span>
            </h1>
            <p style="color: #94a3b8; font-size: 0.88rem; margin-top: 4px;">
                Perbarui nama julukan atau kisah latar belakang petualangmu.
            </p>
        </div>
        <a href="{{ route('characters.show', $character) }}" class="btn-rpg btn-rpg-dark" style="padding: 6px 12px; font-size: 0.8rem;">
            <i class="fa-solid fa-arrow-left"></i> Batal
        </a>
    </div>

    <div class="rpg-card" style="padding: 30px;">
        <form action="{{ route('characters.update', $character) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name" class="form-label">Nama Karakter</label>
                <input type="text" id="name" name="name" value="{{ old('name', $character->name) }}" class="form-input" required maxlength="60">
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div style="background: #0a0c12; padding: 12px; border-radius: 6px; border: 1px solid #1c212e;">
                    <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Ras Leluhur</div>
                    <div style="font-size: 1rem; font-weight: 700; color: var(--gold-bright);">{{ $character->race }}</div>
                </div>

                <div style="background: #0a0c12; padding: 12px; border-radius: 6px; border: 1px solid #1c212e;">
                    <div style="font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Kelas Tempur</div>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $character->class }} (Tingkat {{ $character->level }})</div>
                </div>
            </div>

            <div class="form-group">
                <label for="backstory" class="form-label">Latar Belakang Cerita (Backstory)</label>
                <textarea id="backstory" name="backstory" rows="4" class="form-textarea" placeholder="Tuliskan latar belakang karakter...">{{ old('backstory', $character->backstory) }}</textarea>
                @error('backstory')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('characters.show', $character) }}" class="btn-rpg btn-rpg-dark">Batal</a>
                <button type="submit" class="btn-rpg btn-rpg-gold">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
