@extends('layouts.app')

@section('title', 'Petualangan: ' . $campaign->title . ' - Filkom D&D')

@section('styles')
<style>
    /* Adventure Screen Layout */
    .adventure-grid {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 24px;
        align-items: flex-start;
    }

    /* Narrative Chronicle Stream */
    .narrative-stream {
        background: #0d1017;
        border: 1px solid rgba(212, 175, 55, 0.35);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.6), inset 0 0 60px rgba(0,0,0,0.4);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 160px);
        min-height: 600px;
        position: relative;
        overflow: hidden;
    }

    .chronicle-header {
        background: rgba(19, 23, 34, 0.95);
        border-bottom: 1px solid rgba(212, 175, 55, 0.25);
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .chronicle-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding: 24px 22px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        scroll-behavior: smooth;
    }

    /* Message Bubbles */
    .msg-dm {
        background: rgba(22, 27, 39, 0.85);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-left: 4px solid var(--gold-bright);
        border-radius: 8px;
        padding: 18px 20px;
        position: relative;
        animation: fadeIn 0.35s ease;
    }

    .msg-player {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(59, 130, 246, 0.35);
        border-right: 4px solid #3b82f6;
        border-radius: 8px;
        padding: 14px 18px;
        align-self: flex-end;
        max-width: 85%;
        animation: fadeIn 0.25s ease;
    }

    .msg-system {
        background: rgba(15, 23, 42, 0.8);
        border: 1px dashed rgba(148, 163, 184, 0.3);
        border-radius: 6px;
        padding: 10px 16px;
        font-size: 0.82rem;
        color: #94a3b8;
        align-self: center;
        text-align: center;
        width: 90%;
    }

    .msg-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .narrative-text {
        font-size: 0.96rem;
        line-height: 1.65;
        color: #f1f5f9;
        font-family: var(--font-body);
        white-space: pre-line;
    }

    /* Result Badges */
    .result-badge {
        font-size: 0.72rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .badge-critical_success { background: rgba(16, 185, 129, 0.25); border: 1px solid #10b981; color: #34d399; }
    .badge-success { background: rgba(16, 185, 129, 0.15); border: 1px solid #059669; color: #10b981; }
    .badge-partial_success { background: rgba(245, 158, 11, 0.25); border: 1px solid #f59e0b; color: #fbbf24; }
    .badge-failure { background: rgba(220, 38, 38, 0.25); border: 1px solid #ef4444; color: #f87171; }
    .badge-critical_failure { background: rgba(153, 27, 27, 0.35); border: 1px solid #dc2626; color: #fca5a5; }
    .badge-info { background: rgba(59, 130, 246, 0.2); border: 1px solid #3b82f6; color: #60a5fa; }

    /* Action Input Bar */
    .action-console {
        background: rgba(15, 19, 28, 0.98);
        border-top: 1px solid rgba(212, 175, 55, 0.25);
        padding: 16px 20px;
    }

    .quick-actions {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }
    .quick-btn {
        background: #151a26;
        border: 1px solid #283042;
        color: #cbd5e1;
        font-size: 0.76rem;
        padding: 5px 12px;
        border-radius: 14px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .quick-btn:hover {
        background: rgba(212, 175, 55, 0.15);
        border-color: var(--gold-primary);
        color: var(--gold-bright);
    }

    .input-form-container {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .action-input {
        flex: 1;
        padding: 14px 18px;
        background: #090c12;
        border: 1px solid #283042;
        border-radius: 8px;
        color: #fff;
        font-size: 0.96rem;
        font-family: var(--font-body);
        transition: all 0.2s ease;
    }
    .action-input:focus {
        outline: none;
        border-color: var(--gold-primary);
        box-shadow: 0 0 14px rgba(212, 175, 55, 0.3);
        background: #0e121a;
    }

    /* Dice spinner animation */
    @keyframes spinDice {
        0% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(180deg) scale(1.2); }
        100% { transform: rotate(360deg) scale(1); }
    }
    .spin-dice {
        display: inline-block;
        animation: spinDice 1s infinite linear;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 900px) {
        .adventure-grid {
            grid-template-columns: 1fr;
        }
        .narrative-stream {
            height: 520px;
        }
    }
</style>
@endsection

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
    $hpPercent = $campaign->hpPercentage();
    $hpColor = $hpPercent > 50 ? '#10b981' : ($hpPercent > 20 ? '#f59e0b' : '#ef4444');
    $currentHp = $campaign->current_hp ?? $campaign->max_hp ?? $character->max_hp;
    $maxHp = $campaign->max_hp ?? $character->max_hp;
    $level = $campaign->level ?? 1;
    $status = $campaign->character_status ?? 'Alive';
@endphp

<div class="adventure-grid">
    <!-- Left Column: Character Sheet & Inventory Console -->
    <div>
        <!-- Character Stats Card -->
        <div class="rpg-card gold-border" style="padding: 20px; margin-bottom: 18px; background: #12151f;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                <div style="width: 50px; height: 50px; border-radius: 10px; background: #0c0e14; border: 2px solid var(--gold-primary); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 1.4rem;">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>
                <div style="flex: 1; overflow: hidden;">
                    <h3 class="font-display" style="font-size: 1.15rem; font-weight: 800; color: #fff; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        {{ $character->name }}
                    </h3>
                    <div style="font-size: 0.78rem; color: #94a3b8;">
                        <span style="color: var(--gold-bright);">{{ $character->race }}</span> &bull; {{ $character->class }}
                    </div>
                </div>
                <span id="charLevelBadge" style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.75rem; font-weight: 800; padding: 2px 8px; border-radius: 12px;">
                    Lv. {{ $level }}
                </span>
            </div>

            <!-- Health Bar -->
            <div style="margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px;">
                    <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-heart" style="color: #ef4444; margin-right: 4px;"></i> Hit Points (HP)</span>
                    <span id="charHpText" style="font-weight: 800; color: {{ $hpColor }};">
                        {{ $currentHp }} / {{ $maxHp }}
                    </span>
                </div>
                <div style="height: 10px; background: #0b0d14; border-radius: 5px; overflow: hidden; border: 1px solid #242938;">
                    <div id="charHpBar" style="height: 100%; width: {{ $hpPercent }}%; background: {{ $hpColor }}; transition: width 0.3s ease, background 0.3s ease;"></div>
                </div>
            </div>

            <!-- XP Bar -->
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 4px;">
                    <span style="color: #cbd5e1; font-weight: 600;"><i class="fa-solid fa-star" style="color: #f59e0b; margin-right: 4px;"></i> Pengalaman (XP)</span>
                    <span id="charXpText" style="color: #60a5fa; font-weight: 700;">
                        {{ $campaign->experience }} / {{ $campaign->xpThresholdForNextLevel() }}
                    </span>
                </div>
                <div style="height: 8px; background: #0b0d14; border-radius: 4px; overflow: hidden; border: 1px solid #242938;">
                    <div id="charXpBar" style="height: 100%; width: {{ $campaign->xpPercentage() }}%; background: #3b82f6; transition: width 0.3s ease;"></div>
                </div>
            </div>

            <!-- 6 Ability Scores Compact Grid -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin-bottom: 14px;">
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">STR</div>
                    <div id="dispStr" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->strength }}</div>
                    <div id="modStr" style="font-size: 0.7rem; color: var(--gold-bright);">{{ $character->getModifierString('strength') }}</div>
                </div>
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">DEX</div>
                    <div id="dispDex" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->dexterity }}</div>
                    <div id="modDex" style="font-size: 0.7rem; color: var(--gold-bright);">{{ $character->getModifierString('dexterity') }}</div>
                </div>
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">CON</div>
                    <div id="dispCon" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->constitution }}</div>
                    <div id="modCon" style="font-size: 0.7rem; color: var(--gold-bright);">{{ $character->getModifierString('constitution') }}</div>
                </div>
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">INT</div>
                    <div id="dispInt" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->intelligence }}</div>
                    <div id="modInt" style="font-size: 0.7rem; color: #cbd5e1;">{{ $character->getModifierString('intelligence') }}</div>
                </div>
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">WIS</div>
                    <div id="dispWis" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->wisdom }}</div>
                    <div id="modWis" style="font-size: 0.7rem; color: #cbd5e1;">{{ $character->getModifierString('wisdom') }}</div>
                </div>
                <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 6px; text-align: center;">
                    <div style="font-size: 0.65rem; color: #64748b;">CHA</div>
                    <div id="dispCha" style="font-weight: 800; color: #fff; font-size: 0.95rem;">{{ $character->charisma }}</div>
                    <div id="modCha" style="font-size: 0.7rem; color: #cbd5e1;">{{ $character->getModifierString('charisma') }}</div>
                </div>
            </div>

            <!-- Status Indicator -->
            <div style="display: flex; justify-content: space-between; align-items: center; background: #0a0c12; padding: 8px 12px; border-radius: 6px; border: 1px solid #1c212e; font-size: 0.78rem;">
                <span style="color: #94a3b8;">Kondisi Fisik:</span>
                <span id="charStatusText" style="font-weight: 800; color: {{ $status === 'Alive' ? '#10b981' : '#ef4444' }};">
                    {{ $status === 'Alive' ? 'Sehat Bersemangat' : 'Pingsan / Kritis' }}
                </span>
            </div>
        </div>

        <!-- Inventory Backpack Card -->
        <div class="rpg-card" style="padding: 20px; background: #12151f;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #242938; padding-bottom: 8px;">
                <h4 class="font-display" style="font-size: 1rem; color: #fff; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-bag-shopping" style="color: var(--gold-bright);"></i>
                    <span>Inventaris Tas Ransel</span>
                </h4>
                <span id="invCountBadge" style="font-size: 0.75rem; color: #94a3b8;">
                    {{ $inventories->count() }} Item
                </span>
            </div>

            <div id="inventoryContainer" style="display: flex; flex-direction: column; gap: 8px; max-height: 280px; overflow-y: auto; padding-right: 4px;">
                @forelse ($inventories as $inv)
                    <div class="inv-item-row" data-item-id="{{ $inv->item_id }}" style="background: #0a0c12; border: 1px solid #1c212e; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px; overflow: hidden;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #151824; border: 1px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 0.8rem; flex-shrink: 0;">
                                <i class="fa-solid {{ $inv->item->icon ?: 'fa-box' }}"></i>
                            </div>
                            <div style="overflow: hidden;">
                                <div style="font-size: 0.82rem; font-weight: 700; color: #fff; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                    {{ $inv->item->name }}
                                </div>
                                <div style="font-size: 0.68rem; color: #94a3b8;">
                                    {{ $inv->item->category }} &bull; x{{ $inv->quantity }}
                                    @if ($inv->is_equipped)
                                        <span style="color: #34d399; font-weight: 700;">(Dipakai)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($inv->item->category === 'potion' || $inv->item->effect_type === 'heal' || in_array($inv->item->category, ['weapon', 'armor']))
                            <button type="button" class="btn-use-item" data-item-id="{{ $inv->item_id }}" style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 4px; cursor: pointer; white-space: nowrap;">
                                {{ $inv->item->category === 'potion' || $inv->item->effect_type === 'heal' ? 'Gunakan' : ($inv->is_equipped ? 'Lepas' : 'Pakai') }}
                            </button>
                        @endif
                    </div>
                @empty
                    <div id="invEmptyMsg" style="color: #64748b; font-size: 0.8rem; text-align: center; padding: 14px 0;">
                        Tas masih kosong.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Interactive Digital Chronicle (AI Dungeon Master) -->
    <div>
        <div class="narrative-stream">
            <!-- Chronicle Header -->
            <div class="chronicle-header">
                <div>
                    <h2 class="font-display" style="font-size: 1.15rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-scroll" style="color: var(--gold-bright);"></i>
                        <span>{{ $campaign->title }}</span>
                    </h2>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                        Tingkat: <span style="color: var(--gold-bright);">{{ $campaign->difficulty }}</span> &bull; 
                        Lokasi: <span id="currentLocationText" style="color: #cbd5e1;">{{ $campaign->current_location ?? 'Gerbang Petualangan' }}</span> &bull;
                        Status: <span id="campaignStatusBadge" style="color: {{ $campaign->status === 'Active' ? '#10b981' : '#60a5fa' }}; font-weight: 700;">{{ $campaign->status }}</span>
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('campaigns.show', $campaign) }}" class="btn-rpg btn-rpg-dark" style="padding: 6px 12px; font-size: 0.75rem;" title="Lihat Kronik Lengkap">
                        <i class="fa-solid fa-book"></i> Kronik
                    </a>
                </div>
            </div>

            <!-- Dialogue & Narration Scroll Area -->
            <div class="chronicle-scroll-area" id="chronicleScroll">
                @foreach ($histories as $h)
                    @if ($h->sender_type === 'system')
                        <div class="msg-system">
                            <i class="fa-solid fa-circle-info" style="color: #60a5fa; margin-right: 4px;"></i>
                            {{ $h->narrative }}
                        </div>
                    @else
                        @if ($h->player_action)
                            <div class="msg-player">
                                <div class="msg-header">
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #60a5fa;">
                                        <i class="fa-solid fa-user-ninja"></i> {{ $character->name }}
                                    </span>
                                    <span style="font-size: 0.7rem; color: #64748b;">
                                        {{ $h->created_at->format('H:i') }}
                                    </span>
                                </div>
                                <div style="font-size: 0.95rem; color: #fff; font-style: italic;">
                                    "{{ $h->player_action }}"
                                </div>
                            </div>
                        @endif

                        <div class="msg-dm">
                            <div class="msg-header">
                                <span class="font-display" style="font-size: 0.82rem; font-weight: 700; color: var(--gold-bright); display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-dungeon"></i> Dungeon Master
                                </span>

                                <div style="display: flex; gap: 8px; align-items: center;">
                                    @if ($h->hp_change != 0)
                                        <span style="color: {{ $h->hp_change > 0 ? '#10b981' : '#ef4444' }}; font-weight: 700; font-size: 0.78rem;">
                                            <i class="fa-solid fa-heart"></i> {{ ($h->hp_change > 0 ? '+' : '') . $h->hp_change }} HP
                                        </span>
                                    @endif
                                    @if ($h->xp_change > 0)
                                        <span style="color: #f59e0b; font-weight: 700; font-size: 0.78rem;">
                                            <i class="fa-solid fa-star"></i> +{{ $h->xp_change }} XP
                                        </span>
                                    @endif
                                    @if ($h->action_result)
                                        <span class="result-badge badge-{{ $h->action_result }}">
                                            {{ str_replace('_', ' ', $h->action_result) }}
                                        </span>
                                    @endif
                                    <span style="font-size: 0.7rem; color: #64748b;">
                                        {{ $h->created_at->format('H:i') }}
                                    </span>
                                </div>
                            </div>

                            <div class="narrative-text">{!! nl2br(e($h->narrative)) !!}</div>

                            @if ($h->reasoning)
                                <div style="margin-top: 12px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 4px; padding: 6px 10px; font-size: 0.78rem; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-dice-d20" style="color: var(--gold-bright);"></i>
                                    <span>{{ $h->reasoning }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Bottom Action Console -->
            <div class="action-console">
                <!-- Quick Suggestion Buttons -->
                <div class="quick-actions">
                    <button type="button" class="quick-btn" data-action="Aku memeriksa ruangan dan mencari pintu atau petunjuk rahasia">
                        <i class="fa-solid fa-magnifying-glass"></i> Periksa Sekeliling
                    </button>
                    <button type="button" class="quick-btn" data-action="Aku menghunus senjata dan bersiap menyerang musuh di depanku">
                        <i class="fa-solid fa-sword"></i> Serang Musuh
                    </button>
                    <button type="button" class="quick-btn" data-action="Aku membaca mantra magis untuk mendeteksi energi gaib">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Gunakan Mantra
                    </button>
                    <button type="button" class="quick-btn" data-action="Aku menyapa dengan ramah dan mencoba bernegosiasi">
                        <i class="fa-solid fa-comments"></i> Berbicara / Bujuk
                    </button>
                    <button type="button" class="quick-btn" data-action="Aku mengendap-endap tanpa suara ke dalam bayangan">
                        <i class="fa-solid fa-mask"></i> Menyelinap
                    </button>
                    <button type="button" class="quick-btn" data-action="Aku beristirahat sejenak untuk memulihkan tenaga">
                        <i class="fa-solid fa-camp fire"></i> Istirahat
                    </button>
                </div>

                <!-- Input Form -->
                <form id="actionForm" class="input-form-container">
                    <input type="text" id="actionInput" class="action-input" placeholder="Apa yang ingin kamu lakukan? (Contoh: 'aku membuka pintu besi kuno itu...')" autocomplete="off" required>
                    <button type="submit" id="submitBtn" class="btn-rpg btn-rpg-gold" style="padding: 14px 22px;">
                        <span id="btnIcon"><i class="fa-solid fa-dice-d20"></i></span>
                        <span id="btnText">Tindak</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    const campaignId = {{ $campaign->id }};
    const actionUrl = "{{ route('campaigns.action', $campaign) }}";
    const useItemUrl = "{{ route('campaigns.use-item', $campaign) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const chronicleScroll = document.getElementById('chronicleScroll');
    const actionForm = document.getElementById('actionForm');
    const actionInput = document.getElementById('actionInput');
    const submitBtn = document.getElementById('submitBtn');
    const btnIcon = document.getElementById('btnIcon');
    const btnText = document.getElementById('btnText');

    // UI elements to update dynamically
    const charHpText = document.getElementById('charHpText');
    const charHpBar = document.getElementById('charHpBar');
    const charXpText = document.getElementById('charXpText');
    const charXpBar = document.getElementById('charXpBar');
    const charLevelBadge = document.getElementById('charLevelBadge');
    const charStatusText = document.getElementById('charStatusText');
    const inventoryContainer = document.getElementById('inventoryContainer');
    const invCountBadge = document.getElementById('invCountBadge');

    // Auto-scroll to bottom of chronicle
    function scrollToBottom() {
        chronicleScroll.scrollTop = chronicleScroll.scrollHeight;
    }
    scrollToBottom();

    // Quick action clicks
    document.querySelectorAll('.quick-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            actionInput.value = btn.dataset.action;
            actionInput.focus();
        });
    });

    // Handle Player Action submission via Fetch API (AJAX)
    actionForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const actionText = actionInput.value.trim();
        if (!actionText) return;

        // Immediately append Player Message visually
        appendPlayerMessage(actionText);
        actionInput.value = '';

        // Set Loading state
        setLoading(true);

        try {
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ action: actionText }),
            });

            const data = await response.json();

            if (!response.ok) {
                appendSystemMessage(data.message || 'Gagal memproses tindakan. Silakan coba lagi.');
                setLoading(false);
                return;
            }

            // Append DM narrative response
            appendDmMessage(data);

            // Update Character Vitals
            updateCharacterState(data.character);

            // Update Inventories if modified
            if (data.inventories) {
                renderInventories(data.inventories);
            }

            // Level Up alert if triggered
            if (data.gameplay && data.gameplay.did_level_up) {
                appendSystemMessage(`★ LEVEL UP! ${data.character.name} telah mencapai Level ${data.character.level}! HP maksimum meningkat dan vitalitas dipulihkan!`);
            }

            // Update Campaign Status
            if (data.campaign_status && data.campaign_status === 'Completed') {
                document.getElementById('campaignStatusBadge').textContent = 'Completed';
                document.getElementById('campaignStatusBadge').style.color = '#60a5fa';
                appendSystemMessage('🎉 SELAMAT! Campaign ini telah resmi terselesaikan!');
            }

        } catch (error) {
            console.error('Action error:', error);
            appendSystemMessage('Terjadi kesalahan komunikasi dengan server permainan.');
        } finally {
            setLoading(false);
            scrollToBottom();
        }
    });

    // Helper: Set loading button state
    function setLoading(isLoading) {
        if (isLoading) {
            submitBtn.disabled = true;
            actionInput.disabled = true;
            btnIcon.innerHTML = '<i class="fa-solid fa-dice-d20 spin-dice"></i>';
            btnText.textContent = 'DM Berpikir...';
        } else {
            submitBtn.disabled = false;
            actionInput.disabled = false;
            btnIcon.innerHTML = '<i class="fa-solid fa-dice-d20"></i>';
            btnText.textContent = 'Tindak';
            actionInput.focus();
        }
    }

    // Helper: Append player message to chronicle
    function appendPlayerMessage(action) {
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        const html = `
            <div class="msg-player">
                <div class="msg-header">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #60a5fa;">
                        <i class="fa-solid fa-user-ninja"></i> {{ $character->name }}
                    </span>
                    <span style="font-size: 0.7rem; color: #64748b;">${time}</span>
                </div>
                <div style="font-size: 0.95rem; color: #fff; font-style: italic;">
                    "${escapeHtml(action)}"
                </div>
            </div>
        `;
        chronicleScroll.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Helper: Append DM message to chronicle
    function appendDmMessage(data) {
        let mechanicsBadges = '';
        if (data.gameplay && data.gameplay.hp_delta !== 0) {
            const isPos = data.gameplay.hp_delta > 0;
            mechanicsBadges += `<span style="color: ${isPos ? '#10b981' : '#ef4444'}; font-weight: 700; font-size: 0.78rem;">
                <i class="fa-solid fa-heart"></i> ${isPos ? '+' : ''}${data.gameplay.hp_delta} HP
            </span>`;
        }
        if (data.gameplay && data.gameplay.xp_gain > 0) {
            mechanicsBadges += `<span style="color: #f59e0b; font-weight: 700; font-size: 0.78rem;">
                <i class="fa-solid fa-star"></i> +${data.gameplay.xp_gain} XP
            </span>`;
        }
        if (data.action_result) {
            const cleanResult = data.action_result.replace('_', ' ');
            mechanicsBadges += `<span class="result-badge badge-${data.action_result}">${cleanResult}</span>`;
        }

        let reasoningHtml = '';
        if (data.reasoning) {
            reasoningHtml = `
                <div style="margin-top: 12px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 4px; padding: 6px 10px; font-size: 0.78rem; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-dice-d20" style="color: var(--gold-bright);"></i>
                    <span>${escapeHtml(data.reasoning)}</span>
                </div>
            `;
        }

        const html = `
            <div class="msg-dm">
                <div class="msg-header">
                    <span class="font-display" style="font-size: 0.82rem; font-weight: 700; color: var(--gold-bright); display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-dungeon"></i> Dungeon Master
                    </span>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        ${mechanicsBadges}
                        <span style="font-size: 0.7rem; color: #64748b;">${data.timestamp || 'Baru Saja'}</span>
                    </div>
                </div>
                <div class="narrative-text">${escapeHtml(data.narrative)}</div>
                ${reasoningHtml}
            </div>
        `;

        chronicleScroll.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Helper: Append system notice
    function appendSystemMessage(msg) {
        const html = `
            <div class="msg-system">
                <i class="fa-solid fa-circle-info" style="color: #60a5fa; margin-right: 4px;"></i>
                ${escapeHtml(msg)}
            </div>
        `;
        chronicleScroll.insertAdjacentHTML('beforeend', html);
        scrollToBottom();
    }

    // Helper: Update dynamic character health, level, xp
    function updateCharacterState(char) {
        if (!char) return;

        charHpText.textContent = `${char.current_hp} / ${char.max_hp}`;
        charHpBar.style.width = `${char.hp_percent}%`;
        const hpColor = char.hp_percent > 50 ? '#10b981' : (char.hp_percent > 20 ? '#f59e0b' : '#ef4444');
        charHpBar.style.background = hpColor;
        charHpText.style.color = hpColor;

        charXpText.textContent = `${char.experience} / ${char.xp_target}`;
        charXpBar.style.width = `${char.xp_percent}%`;

        charLevelBadge.textContent = `Lv. ${char.level}`;

        if (char.status === 'Alive') {
            charStatusText.textContent = 'Sehat Bersemangat';
            charStatusText.style.color = '#10b981';
        } else {
            charStatusText.textContent = 'Pingsan / Kritis';
            charStatusText.style.color = '#ef4444';
        }

        // Stats & modifiers if present
        if (char.stats) {
            document.getElementById('dispStr').textContent = char.stats.strength;
            document.getElementById('dispDex').textContent = char.stats.dexterity;
            document.getElementById('dispCon').textContent = char.stats.constitution;
            document.getElementById('dispInt').textContent = char.stats.intelligence;
            document.getElementById('dispWis').textContent = char.stats.wisdom;
            document.getElementById('dispCha').textContent = char.stats.charisma;
        }
        if (char.modifiers) {
            document.getElementById('modStr').textContent = char.modifiers.strength;
            document.getElementById('modDex').textContent = char.modifiers.dexterity;
            document.getElementById('modCon').textContent = char.modifiers.constitution;
            document.getElementById('modInt').textContent = char.modifiers.intelligence;
            document.getElementById('modWis').textContent = char.modifiers.wisdom;
            document.getElementById('modCha').textContent = char.modifiers.charisma;
        }
    }

    // Helper: Render inventory list dynamically
    function renderInventories(items) {
        if (!items || items.length === 0) {
            inventoryContainer.innerHTML = '<div id="invEmptyMsg" style="color: #64748b; font-size: 0.8rem; text-align: center; padding: 14px 0;">Tas masih kosong.</div>';
            invCountBadge.textContent = '0 Item';
            return;
        }

        invCountBadge.textContent = `${items.length} Item`;
        let html = '';

        items.forEach(inv => {
            const iconClass = inv.icon || 'fa-box';
            const isEquippedText = inv.is_equipped ? '<span style="color: #34d399; font-weight: 700;">(Dipakai)</span>' : '';
            let btnActionText = 'Gunakan';
            if (inv.category === 'weapon' || inv.category === 'armor') {
                btnActionText = inv.is_equipped ? 'Lepas' : 'Pakai';
            }

            const btnHtml = inv.is_usable ? `
                <button type="button" class="btn-use-item" data-item-id="${inv.item_id}" style="background: rgba(212, 175, 55, 0.15); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 4px; cursor: pointer; white-space: nowrap;">
                    ${btnActionText}
                </button>
            ` : '';

            html += `
                <div class="inv-item-row" data-item-id="${inv.item_id}" style="background: #0a0c12; border: 1px solid #1c212e; border-radius: 6px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px; overflow: hidden;">
                        <div style="width: 28px; height: 28px; border-radius: 6px; background: #151824; border: 1px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center; color: var(--gold-bright); font-size: 0.8rem; flex-shrink: 0;">
                            <i class="fa-solid ${iconClass}"></i>
                        </div>
                        <div style="overflow: hidden;">
                            <div style="font-size: 0.82rem; font-weight: 700; color: #fff; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                                ${escapeHtml(inv.name)}
                            </div>
                            <div style="font-size: 0.68rem; color: #94a3b8;">
                                ${inv.category} &bull; x${inv.quantity} ${isEquippedText}
                            </div>
                        </div>
                    </div>
                    ${btnHtml}
                </div>
            `;
        });

        inventoryContainer.innerHTML = html;
        attachInventoryItemListeners();
    }

    // Attach listeners for "Gunakan / Pakai" buttons
    function attachInventoryItemListeners() {
        document.querySelectorAll('.btn-use-item').forEach(btn => {
            btn.onclick = async () => {
                const itemId = btn.dataset.itemId;
                btn.disabled = true;
                btn.textContent = '...';

                try {
                    const response = await fetch(useItemUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ item_id: itemId }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        appendSystemMessage(data.message || 'Gagal menggunakan item.');
                        return;
                    }

                    appendSystemMessage(data.message);
                    if (data.character) {
                        updateCharacterState(data.character);
                    }
                    if (data.inventories) {
                        renderInventories(data.inventories);
                    }
                } catch (e) {
                    console.error('Use item error:', e);
                    appendSystemMessage('Gagal menggunakan item.');
                }
            };
        });
    }

    attachInventoryItemListeners();

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
@endsection
