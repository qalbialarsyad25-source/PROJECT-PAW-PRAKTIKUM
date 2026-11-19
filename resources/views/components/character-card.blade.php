@props(['character'])

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

<div class="rpg-card" style="padding: 22px; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <!-- Card Header -->
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 14px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 10px; background: #0b0d14; border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 1.3rem;">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>
                <div>
                    <h3 class="font-display" style="font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 2px;">
                        {{ $character->name }}
                    </h3>
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #94a3b8;">
                        <span style="color: var(--gold-bright);">{{ $character->race }}</span>
                        <span>&bull;</span>
                        <span>{{ $character->class }}</span>
                    </div>
                </div>
            </div>
            <span style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 20px;">
                Lv. {{ $character->level }}
            </span>
        </div>

        <!-- HP Bar -->
        <div style="margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 4px;">
                <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-heart" style="color: #ef4444; margin-right: 4px;"></i> HP</span>
                <span style="font-weight: 700; color: {{ $hpColor }};">{{ $character->current_hp }} / {{ $character->max_hp }}</span>
            </div>
            <div style="height: 8px; background: #0c0e14; border-radius: 4px; overflow: hidden; border: 1px solid #242938;">
                <div style="height: 100%; width: {{ $hpPercent }}%; background: {{ $hpColor }}; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <!-- XP Bar -->
        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 4px;">
                <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 4px;"></i> XP</span>
                <span style="color: #94a3b8;">{{ $character->experience }} / {{ $character->xpThresholdForNextLevel() }}</span>
            </div>
            <div style="height: 6px; background: #0c0e14; border-radius: 3px; overflow: hidden; border: 1px solid #242938;">
                <div style="height: 100%; width: {{ $character->xpPercentage() }}%; background: #3b82f6;"></div>
            </div>
        </div>

        <!-- Stats Micro Bar -->
        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 4px; background: #0a0c12; padding: 8px; border-radius: 6px; margin-bottom: 18px; text-align: center; border: 1px solid #1e2330;">
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">STR</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->strength }}</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">DEX</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->dexterity }}</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">CON</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->constitution }}</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">INT</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->intelligence }}</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">WIS</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->wisdom }}</div>
            </div>
            <div>
                <div style="font-size: 0.65rem; color: #64748b;">CHA</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #cbd5e1;">{{ $character->charisma }}</div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; gap: 8px; border-top: 1px solid #1f2433; padding-top: 14px;">
        <a href="{{ route('characters.show', $character) }}" class="btn-rpg btn-rpg-dark" style="flex: 1; padding: 8px 12px; font-size: 0.8rem;">
            <i class="fa-solid fa-eye"></i> Detail
        </a>
        <a href="{{ route('campaigns.create', ['character_id' => $character->id]) }}" class="btn-rpg btn-rpg-gold" style="flex: 1.3; padding: 8px 12px; font-size: 0.8rem;">
            <i class="fa-solid fa-play"></i> Petualangan
        </a>
    </div>
</div>
