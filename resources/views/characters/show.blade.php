@extends('layouts.app')

@section('title', $character->name . ' - Lembar Karakter')

@section('content')
@php
    $classIcons = [
        'Warrior' => 'fa-shield-halved',
        'Mage' => 'fa-wand-magic-sparkles',
        'Archer' => 'fa-bow-arrow',
        'Rogue' => 'fa-dagger',
        'Cleric' => 'fa-cross',
    ];
    $icon = $classIcons[$character->class] ?? 'fa-user';
    $hpPercent = $character->hpPercentage();
    $hpColor = $hpPercent > 50 ? '#10b981' : ($hpPercent > 20 ? '#f59e0b' : '#ef4444');
@endphp

<div style="max-width: 1100px; margin: 0 auto;">
    <!-- Top Action Breadcrumbs -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('characters.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 0.88rem; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Karakter
        </a>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('campaigns.create', ['character_id' => $character->id]) }}" class="btn-rpg btn-rpg-gold" style="padding: 8px 16px; font-size: 0.82rem;">
                <i class="fa-solid fa-play"></i> Mulai Petualangan
            </a>
            <a href="{{ route('characters.edit', $character) }}" class="btn-rpg btn-rpg-dark" style="padding: 8px 14px; font-size: 0.82rem;">
                <i class="fa-solid fa-pen-to-square"></i> Edit
            </a>
            <form action="{{ route('characters.destroy', $character) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karakter {{ $character->name }}? Tindakan ini permanen dan akan menghapus semua riwayat campaign karakter ini.');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-rpg btn-rpg-danger" style="padding: 8px 14px; font-size: 0.82rem;">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Character Sheet Header Card -->
    <div class="rpg-card gold-border" style="padding: 28px; margin-bottom: 24px; background: linear-gradient(135deg, rgba(20, 24, 34, 0.95) 0%, rgba(11, 13, 19, 0.98) 100%);">
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <div style="width: 80px; height: 80px; border-radius: 14px; background: #0c0e14; border: 2px solid var(--gold-primary); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 2.2rem; box-shadow: 0 0 20px rgba(212, 175, 55, 0.25);">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
                    <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff;">
                        {{ $character->name }}
                    </h1>
                    <span style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.78rem; font-weight: 700; padding: 2px 10px; border-radius: 20px;">
                        Tingkat / Level {{ $character->level }}
                    </span>
                    <span style="background: {{ $character->status === 'Alive' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(220, 38, 38, 0.2)' }}; border: 1px solid {{ $character->status === 'Alive' ? '#10b981' : '#dc2626' }}; color: {{ $character->status === 'Alive' ? '#34d399' : '#f87171' }}; font-size: 0.78rem; font-weight: 700; padding: 2px 10px; border-radius: 20px;">
                        {{ $character->status === 'Alive' ? 'Sehat (Alive)' : 'Pingsan (Unconscious)' }}
                    </span>
                </div>
                <div style="display: flex; gap: 12px; font-size: 0.9rem; color: #94a3b8; align-items: center;">
                    <span style="color: var(--gold-bright); font-weight: 600;"><i class="fa-solid fa-dna"></i> Ras {{ $character->race }}</span>
                    <span>&bull;</span>
                    <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-shield"></i> Kelas {{ $character->class }}</span>
                    <span>&bull;</span>
                    <span>Dibuat: {{ $character->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        @if ($character->backstory)
            <div style="margin-top: 20px; padding: 14px; background: rgba(0, 0, 0, 0.3); border-left: 3px solid var(--gold-bright); border-radius: 0 8px 8px 0; font-size: 0.88rem; color: #cbd5e1; line-height: 1.5; font-style: italic;">
                "{{ $character->backstory }}"
            </div>
        @endif
    </div>

    <!-- Health & Experience Vitals Bars -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
        <div class="rpg-card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="font-display" style="font-weight: 700; color: #fff; font-size: 0.95rem;">
                    <i class="fa-solid fa-heart" style="color: #ef4444; margin-right: 6px;"></i> Vitalitas Tubuh (Hit Points)
                </span>
                <span style="font-weight: 800; font-size: 1.1rem; color: {{ $hpColor }};">
                    {{ $character->current_hp }} / {{ $character->max_hp }} HP
                </span>
            </div>
            <div style="height: 12px; background: #0b0d14; border-radius: 6px; overflow: hidden; border: 1px solid #242938;">
                <div style="height: 100%; width: {{ $hpPercent }}%; background: {{ $hpColor }}; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <div class="rpg-card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span class="font-display" style="font-weight: 700; color: #fff; font-size: 0.95rem;">
                    <i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 6px;"></i> Pengalaman (Experience)
                </span>
                <span style="font-weight: 800; font-size: 1.1rem; color: #60a5fa;">
                    {{ $character->experience }} / {{ $character->xpThresholdForNextLevel() }} XP
                </span>
            </div>
            <div style="height: 12px; background: #0b0d14; border-radius: 6px; overflow: hidden; border: 1px solid #242938;">
                <div style="height: 100%; width: {{ $character->xpPercentage() }}%; background: #3b82f6; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    <!-- 6 D&D Ability Scores Sheet -->
    <div class="rpg-card" style="padding: 24px; margin-bottom: 24px;">
        <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-dice-d20"></i> Enam Atribut Utama (D&D Ability Scores)
        </h3>
        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px;">
            <x-stat-badge name="STR" fullName="Strength" :score="$character->strength" :modifier="$character->getModifierString('strength')" icon="fa-hand-fist" />
            <x-stat-badge name="DEX" fullName="Dexterity" :score="$character->dexterity" :modifier="$character->getModifierString('dexterity')" icon="fa-person-running" />
            <x-stat-badge name="CON" fullName="Constitution" :score="$character->constitution" :modifier="$character->getModifierString('constitution')" icon="fa-shield-halved" />
            <x-stat-badge name="INT" fullName="Intelligence" :score="$character->intelligence" :modifier="$character->getModifierString('intelligence')" icon="fa-brain" />
            <x-stat-badge name="WIS" fullName="Wisdom" :score="$character->wisdom" :modifier="$character->getModifierString('wisdom')" icon="fa-eye" />
            <x-stat-badge name="CHA" fullName="Charisma" :score="$character->charisma" :modifier="$character->getModifierString('charisma')" icon="fa-comments" />
        </div>
    </div>

    <!-- Two Columns: Inventory & Campaigns -->
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px;">
        <!-- Inventory Panel -->
        <div class="rpg-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid #242938; padding-bottom: 12px;">
                <h3 class="font-display" style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-bag-shopping" style="color: var(--gold-bright);"></i>
                    <span>Inventaris Perlengkapan</span>
                </h3>
                <span style="font-size: 0.8rem; color: #94a3b8;">
                    {{ $character->inventories->count() }} Jenis Item
                </span>
            </div>

            @if ($character->inventories->isEmpty())
                <p style="color: #64748b; font-size: 0.88rem; text-align: center; padding: 20px 0;">
                    Tas ransel karakter ini masih kosong.
                </p>
            @else
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach ($character->inventories as $inv)
                        <div style="background: #0d0f17; border: 1px solid #202636; border-radius: 8px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 38px; height: 38px; border-radius: 8px; background: #151924; border: 1px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 1rem;">
                                    <i class="fa-solid {{ $inv->item->icon ?: 'fa-box' }}"></i>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <span style="font-weight: 700; color: #fff; font-size: 0.9rem;">{{ $inv->item->name }}</span>
                                        @if ($inv->is_equipped)
                                            <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #34d399; font-size: 0.68rem; font-weight: 700; padding: 1px 6px; border-radius: 4px;">
                                                Dipakai
                                            </span>
                                        @endif
                                    </div>
                                    <div style="font-size: 0.75rem; color: #94a3b8; line-height: 1.3;">
                                        {{ $inv->item->description }}
                                    </div>
                                </div>
                            </div>
                            <span style="background: #181d2a; border: 1px solid #2b3345; color: var(--gold-bright); font-size: 0.82rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; white-space: nowrap;">
                                x{{ $inv->quantity }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Campaigns History -->
        <div class="rpg-card" style="padding: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid #242938; padding-bottom: 12px;">
                <h3 class="font-display" style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map" style="color: var(--gold-bright);"></i>
                    <span>Ekspedisi Campaign</span>
                </h3>
                <span style="font-size: 0.8rem; color: #94a3b8;">
                    {{ $character->campaigns->count() }} Petualangan
                </span>
            </div>

            @if ($character->campaigns->isEmpty())
                <p style="color: #64748b; font-size: 0.85rem; text-align: center; padding: 20px 0;">
                    Karakter ini belum pernah menjelajahi campaign apapun.
                </p>
                <div style="text-align: center;">
                    <a href="{{ route('campaigns.create', ['character_id' => $character->id]) }}" class="btn-rpg btn-rpg-gold" style="padding: 8px 14px; font-size: 0.8rem;">
                        <i class="fa-solid fa-play"></i> Buka Ekspedisi Pertama
                    </a>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach ($character->campaigns as $camp)
                        <div style="background: #0d0f17; border: 1px solid #202636; border-radius: 8px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h4 class="font-display" style="font-size: 0.92rem; color: #fff; font-weight: 700;">{{ $camp->title }}</h4>
                                <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                                    Tingkat: <span style="color: var(--gold-bright);">{{ $camp->difficulty }}</span> &bull; Status: <span style="color: {{ $camp->status === 'Active' ? '#34d399' : '#60a5fa' }};">{{ $camp->status }}</span>
                                </div>
                            </div>
                            <a href="{{ route('campaigns.adventure', $camp) }}" class="btn-rpg btn-rpg-gold" style="padding: 6px 10px; font-size: 0.75rem;">
                                <i class="fa-solid fa-dice-d20"></i> Buka
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
