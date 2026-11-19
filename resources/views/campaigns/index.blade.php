@extends('layouts.app')

@section('title', 'Daftar Campaign Petualangan')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 18px; flex-wrap: wrap; gap: 14px;">
    <div>
        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-dungeon" style="color: var(--gold-bright);"></i>
            <span>Daftar Campaign Petualangan</span>
        </h1>
        <p style="color: #94a3b8; font-size: 0.92rem; margin-top: 4px;">
            Kelola setiap ekspedisi penjelajahan dungeon dan kisah petualangan solo Anda.
        </p>
    </div>

    <a href="{{ route('campaigns.create') }}" class="btn-rpg btn-rpg-gold">
        <i class="fa-solid fa-play"></i> Mulai Campaign Baru
    </a>
</div>

<!-- Active Campaigns Section -->
<div style="margin-bottom: 40px;">
    <h2 class="font-display" style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-fire" style="color: var(--gold-bright);"></i>
        <span>Campaign Yang Sedang Berjalan ({{ $activeCampaigns->count() }})</span>
    </h2>

    @if ($activeCampaigns->isEmpty())
        <div class="rpg-card" style="padding: 32px 20px; text-align: center;">
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 16px;">Tidak ada campaign yang sedang berjalan saat ini.</p>
            <a href="{{ route('campaigns.create') }}" class="btn-rpg btn-rpg-gold" style="font-size: 0.82rem; padding: 8px 16px;">
                <i class="fa-solid fa-play"></i> Mulai Campaign Baru
            </a>
        </div>
    @else
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            @foreach ($activeCampaigns as $camp)
                <div class="rpg-card" style="padding: 24px; display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid var(--gold-bright);">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="background: rgba(245, 158, 11, 0.15); color: var(--gold-bright); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                {{ $camp->difficulty }}
                            </span>
                            <span style="font-size: 0.78rem; color: #64748b;">
                                <i class="fa-solid fa-calendar-days" style="margin-right: 4px;"></i> {{ $camp->created_at->format('d M Y') }}
                            </span>
                        </div>

                        <h3 class="font-display" style="font-size: 1.2rem; font-weight: 700; color: #fff; margin-bottom: 8px;">
                            {{ $camp->title }}
                        </h3>

                        <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.4; margin-bottom: 16px;">
                            {{ Str::limit($camp->description ?? 'Sebuah ekspedisi penjelajahan misteri yang diarahkan langsung oleh AI Dungeon Master.', 120) }}
                        </p>

                        <!-- Character Badge -->
                        <div style="background: #0a0c12; border: 1px solid #1c212e; border-radius: 6px; padding: 10px 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-user-shield" style="color: var(--gold-bright);"></i>
                                <span style="font-weight: 700; font-size: 0.85rem; color: #fff;">{{ $camp->character->name }}</span>
                            </div>
                            <span style="font-size: 0.75rem; color: #94a3b8;">
                                {{ $camp->character->race }} {{ $camp->character->class }} &bull; Lv. {{ $camp->character->level }}
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; border-top: 1px solid #1f2433; padding-top: 14px;">
                        <a href="{{ route('campaigns.adventure', $camp) }}" class="btn-rpg btn-rpg-gold" style="flex: 1; padding: 8px 12px; font-size: 0.82rem;">
                            <i class="fa-solid fa-dice-d20"></i> Lanjutkan
                        </a>
                        <a href="{{ route('campaigns.show', $camp) }}" class="btn-rpg btn-rpg-dark" style="padding: 8px 12px; font-size: 0.82rem;" title="Kronik">
                            <i class="fa-solid fa-book-open"></i>
                        </a>
                        <form action="{{ route('campaigns.destroy', $camp) }}" method="POST" onsubmit="return confirm('Hapus campaign {{ $camp->title }}? Riwayat cerita akan dihapus.');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-rpg btn-rpg-danger" style="padding: 8px 12px; font-size: 0.82rem;" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Completed Campaigns Section -->
@if ($completedCampaigns->isNotEmpty())
    <div>
        <h2 class="font-display" style="font-size: 1.25rem; font-weight: 700; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-trophy" style="color: #10b981;"></i>
            <span>Campaign Yang Telah Selesai ({{ $completedCampaigns->count() }})</span>
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
            @foreach ($completedCampaigns as $comp)
                <div class="rpg-card" style="padding: 20px; border-left: 4px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-size: 0.72rem; color: #10b981; font-weight: 700; background: rgba(16, 185, 129, 0.15); padding: 2px 8px; border-radius: 4px;">
                            {{ $comp->status }}
                        </span>
                        <span style="font-size: 0.75rem; color: #64748b;">
                            {{ $comp->updated_at->format('d M Y') }}
                        </span>
                    </div>

                    <h3 class="font-display" style="font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 6px;">
                        {{ $comp->title }}
                    </h3>

                    <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 14px;">
                        Karakter: <strong style="color: #cbd5e1;">{{ $comp->character->name }}</strong> (Lv. {{ $comp->character->level }})
                    </p>

                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('campaigns.show', $comp) }}" class="btn-rpg btn-rpg-dark" style="flex: 1; padding: 6px 12px; font-size: 0.78rem;">
                            <i class="fa-solid fa-scroll"></i> Baca Kronik Petualangan
                        </a>
                        <form action="{{ route('campaigns.destroy', $comp) }}" method="POST" onsubmit="return confirm('Hapus campaign {{ $comp->title }}?');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-rpg btn-rpg-danger" style="padding: 6px 10px; font-size: 0.78rem;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
