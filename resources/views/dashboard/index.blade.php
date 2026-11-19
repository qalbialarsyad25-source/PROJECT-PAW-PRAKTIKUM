@extends('layouts.app')

@section('title', 'Markas Petualang (Dashboard)')

@section('content')
<!-- Hero Welcome Section -->
<div class="rpg-card gold-border" style="padding: 32px 28px; margin-bottom: 30px; background: linear-gradient(135deg, rgba(25, 29, 41, 0.95) 0%, rgba(13, 16, 23, 0.98) 100%);">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.35); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; color: var(--gold-bright); margin-bottom: 10px;">
                <i class="fa-solid fa-crown"></i> Petualang Tingkat {{ $highestLevel }}
            </div>
            <h1 class="font-display" style="font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 6px;">
                Salam Hangat, <span class="gold-text-glow">{{ $user->name }}</span>!
            </h1>
            <p style="color: #94a3b8; font-size: 0.95rem; max-width: 650px; line-height: 1.5;">
                Selamat datang di markas besar petualangan Filkom D&D. Dunia penuh misteri, ruang bawah tanah terkutuk, dan monster perkasa telah menunggu keputusan serta keberanianmu bersama AI Dungeon Master.
            </p>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
                <i class="fa-solid fa-plus-circle"></i> Ciptakan Karakter
            </a>
            <a href="{{ route('campaigns.create') }}" class="btn-rpg btn-rpg-dark">
                <i class="fa-solid fa-play"></i> Mulai Campaign Baru
            </a>
        </div>
    </div>
</div>

<!-- Game Metrics Overview -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 36px;">
    <div class="rpg-card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #60a5fa;">
            <i class="fa-solid fa-users-viewfinder"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Total Karakter</div>
            <div class="font-display" style="font-size: 1.7rem; font-weight: 800; color: #fff;">{{ $totalCharacters }}</div>
        </div>
    </div>

    <div class="rpg-card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: var(--gold-bright);">
            <i class="fa-solid fa-fire"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Campaign Aktif</div>
            <div class="font-display" style="font-size: 1.7rem; font-weight: 800; color: #fff;">{{ $activeCount }}</div>
        </div>
    </div>

    <div class="rpg-card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #34d399;">
            <i class="fa-solid fa-trophy"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Campaign Selesai</div>
            <div class="font-display" style="font-size: 1.7rem; font-weight: 800; color: #fff;">{{ $completedCount }}</div>
        </div>
    </div>

    <div class="rpg-card" style="padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.4); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #c084fc;">
            <i class="fa-solid fa-shield-virus"></i>
        </div>
        <div>
            <div style="font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Level Tertinggi</div>
            <div class="font-display" style="font-size: 1.7rem; font-weight: 800; color: #fff;">Lv. {{ $highestLevel }}</div>
        </div>
    </div>
</div>

<!-- Main Sections Grid -->
<div style="display: grid; grid-template-columns: 1fr; gap: 36px;">

    <!-- Active Campaigns Section -->
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 10px;">
            <h2 class="font-display" style="font-size: 1.35rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-compass" style="color: var(--gold-bright);"></i>
                <span>Campaign Yang Sedang Berlangsung</span>
            </h2>
            <a href="{{ route('campaigns.index') }}" style="color: var(--gold-bright); text-decoration: none; font-size: 0.88rem; font-weight: 600;">
                Lihat Semua ({{ $totalCampaigns }}) <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>

        @if ($activeCampaigns->isEmpty())
            <div class="rpg-card" style="padding: 36px 20px; text-align: center;">
                <div style="font-size: 2.2rem; color: #475569; margin-bottom: 12px;">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h3 class="font-display" style="color: #cbd5e1; font-size: 1.1rem; margin-bottom: 6px;">Belum Ada Petualangan Aktif</h3>
                <p style="color: #64748b; font-size: 0.88rem; max-width: 450px; margin: 0 auto 18px;">
                    Pilih salah satu karakter Anda dan mulailah petualangan RPG solo pertama Anda bersama Dungeon Master AI.
                </p>
                @if ($characters->isNotEmpty())
                    <a href="{{ route('campaigns.create') }}" class="btn-rpg btn-rpg-gold">
                        <i class="fa-solid fa-play"></i> Mulai Petualangan Sekarang
                    </a>
                @else
                    <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
                        <i class="fa-solid fa-user-plus"></i> Ciptakan Karakter Terlebih Dahulu
                    </a>
                @endif
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px;">
                @foreach ($activeCampaigns as $camp)
                    <div class="rpg-card" style="padding: 22px; display: flex; flex-direction: column; justify-content: space-between; border-left: 4px solid var(--gold-bright);">
                        <div>
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                <span style="background: rgba(245, 158, 11, 0.15); color: var(--gold-bright); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                    {{ $camp->difficulty }}
                                </span>
                                <span style="font-size: 0.78rem; color: #64748b;">
                                    <i class="fa-solid fa-clock" style="margin-right: 4px;"></i> {{ $camp->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="font-display" style="font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 6px;">
                                {{ $camp->title }}
                            </h3>

                            <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.4; margin-bottom: 14px;">
                                {{ Str::limit($camp->description ?? 'Sebuah ekspedisi misterius melintasi lorong-lorong kuno yang penuh bahaya.', 110) }}
                            </p>

                            <div style="background: #0a0c12; border: 1px solid #1c212e; border-radius: 6px; padding: 10px 12px; margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
                                <div style="width: 32px; height: 32px; border-radius: 6px; background: #141824; border: 1px solid var(--gold-primary); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div style="flex: 1; overflow: hidden;">
                                    <div style="font-size: 0.82rem; font-weight: 700; color: #fff; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                        {{ $camp->character->name }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #94a3b8;">
                                        {{ $camp->character->race }} {{ $camp->character->class }} &bull; Lv. {{ $camp->character->level }} &bull; {{ $camp->character->current_hp }}/{{ $camp->character->max_hp }} HP
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('campaigns.adventure', $camp) }}" class="btn-rpg btn-rpg-gold" style="flex: 1; padding: 9px 12px; font-size: 0.82rem;">
                                <i class="fa-solid fa-dice-d20"></i> Lanjutkan Cerita
                            </a>
                            <a href="{{ route('campaigns.show', $camp) }}" class="btn-rpg btn-rpg-dark" style="padding: 9px 12px; font-size: 0.82rem;" title="Lihat Kronik">
                                <i class="fa-solid fa-book-open"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- My Characters Roster Section -->
    <div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 10px;">
            <h2 class="font-display" style="font-size: 1.35rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-user-ninja" style="color: var(--gold-bright);"></i>
                <span>Karakter Petualang Milik Anda</span>
            </h2>
            <div style="display: flex; gap: 12px; align-items: center;">
                <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold" style="padding: 6px 12px; font-size: 0.78rem;">
                    <i class="fa-solid fa-plus"></i> Karakter Baru
                </a>
                <a href="{{ route('characters.index') }}" style="color: var(--gold-bright); text-decoration: none; font-size: 0.88rem; font-weight: 600;">
                    Semua ({{ $totalCharacters }}) <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i>
                </a>
            </div>
        </div>

        @if ($characters->isEmpty())
            <div class="rpg-card" style="padding: 36px 20px; text-align: center;">
                <div style="font-size: 2.2rem; color: #475569; margin-bottom: 12px;">
                    <i class="fa-solid fa-helmet-un"></i>
                </div>
                <h3 class="font-display" style="color: #cbd5e1; font-size: 1.1rem; margin-bottom: 6px;">Belum Ada Karakter</h3>
                <p style="color: #64748b; font-size: 0.88rem; max-width: 450px; margin: 0 auto 18px;">
                    Setiap pahlawan membutuhkan awal mula. Pilih ras dan kelas karakter favorit Anda, kembangkan statistiknya, dan dapatkan senjata awal.
                </p>
                <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
                    <i class="fa-solid fa-plus-circle"></i> Ciptakan Karakter Sekarang
                </a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
                @foreach ($characters as $char)
                    <x-character-card :character="$char" />
                @endforeach
            </div>
        @endif
    </div>

    <!-- Completed Campaigns Hall of Fame (If any) -->
    @if ($completedCampaigns->isNotEmpty())
        <div>
            <div style="margin-bottom: 18px; border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding-bottom: 10px;">
                <h2 class="font-display" style="font-size: 1.25rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-award" style="color: #10b981;"></i>
                    <span>Aula Kemenangan (Campaign Terselesaikan)</span>
                </h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
                @foreach ($completedCampaigns as $compCamp)
                    <div class="rpg-card" style="padding: 18px; border-left: 4px solid #10b981;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <h4 class="font-display" style="font-size: 1rem; color: #fff; font-weight: 700;">{{ $compCamp->title }}</h4>
                            <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.72rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                Selesai
                            </span>
                        </div>
                        <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 12px;">
                            Pahlawan: <strong style="color: #fff;">{{ $compCamp->character->name }}</strong> (Lv. {{ $compCamp->character->level }})
                        </p>
                        <a href="{{ route('campaigns.show', $compCamp) }}" class="btn-rpg btn-rpg-dark" style="padding: 6px 12px; font-size: 0.78rem; width: 100%;">
                            <i class="fa-solid fa-scroll"></i> Baca Riwayat Penuh Petualangan
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
