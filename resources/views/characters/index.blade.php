@extends('layouts.app')

@section('title', 'Daftar Karakter Petualang')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 18px; flex-wrap: wrap; gap: 14px;">
    <div>
        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-users" style="color: var(--gold-bright);"></i>
            <span>Daftar Karakter Petualang</span>
        </h1>
        <p style="color: #94a3b8; font-size: 0.92rem; margin-top: 4px;">
            Seluruh pahlawan yang Anda ciptakan untuk menjelajahi dunia fantasi Filkom D&D.
        </p>
    </div>

    <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
        <i class="fa-solid fa-user-plus"></i> Ciptakan Karakter Baru
    </a>
</div>

@if ($characters->isEmpty())
    <div class="rpg-card" style="padding: 48px 24px; text-align: center;">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: #131722; border: 2px dashed var(--gold-primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--gold-bright); margin: 0 auto 16px;">
            <i class="fa-solid fa-shield-cat"></i>
        </div>
        <h3 class="font-display" style="font-size: 1.25rem; color: #fff; margin-bottom: 8px;">Anda Belum Memiliki Karakter</h3>
        <p style="color: #94a3b8; font-size: 0.9rem; max-width: 480px; margin: 0 auto 24px;">
            Mulailah petualangan solo Anda dengan memilih ras dan kelas karakter idaman Anda. Setiap kelas dibekali perlengkapan awal dan statistik D&D unik.
        </p>
        <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
            <i class="fa-solid fa-plus-circle"></i> Mulai Ciptakan Karakter
        </a>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; margin-bottom: 30px;">
        @foreach ($characters as $char)
            <x-character-card :character="$char" />
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-top: 24px;">
        {{ $characters->links() }}
    </div>
@endif
@endsection
