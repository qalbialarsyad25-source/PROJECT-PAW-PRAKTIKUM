@extends('layouts.app')

@section('title', 'Kronik Petualangan - ' . $campaign->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <!-- Top Action Breadcrumbs -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('campaigns.index') }}" style="color: #94a3b8; text-decoration: none; font-size: 0.88rem; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Campaign
        </a>

        @if ($campaign->isActive())
            <a href="{{ route('campaigns.adventure', $campaign) }}" class="btn-rpg btn-rpg-gold" style="padding: 8px 18px; font-size: 0.85rem;">
                <i class="fa-solid fa-dice-d20"></i> Masuki Ruang Petualangan (AI DM)
            </a>
        @endif
    </div>

    <!-- Campaign Lore Header Card -->
    <div class="rpg-card gold-border" style="padding: 28px; margin-bottom: 28px; background: linear-gradient(135deg, rgba(22, 26, 38, 0.95) 0%, rgba(10, 12, 18, 0.98) 100%);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px; flex-wrap: wrap;">
            <span style="background: rgba(212, 175, 55, 0.2); border: 1px solid var(--gold-primary); color: var(--gold-bright); font-size: 0.78rem; font-weight: 700; padding: 2px 10px; border-radius: 4px; text-transform: uppercase;">
                Kesulitan: {{ $campaign->difficulty }}
            </span>
            <span style="font-size: 0.82rem; color: #94a3b8;">
                Dimulai: {{ $campaign->created_at->format('d F Y, H:i') }}
            </span>
        </div>

        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 10px;">
            {{ $campaign->title }}
        </h1>

        <div style="display: flex; gap: 14px; font-size: 0.88rem; color: #cbd5e1; margin-bottom: 16px; align-items: center; flex-wrap: wrap;">
            <span>Pahlawan: <strong style="color: var(--gold-bright);">{{ $campaign->character->name }}</strong> ({{ $campaign->character->race }} {{ $campaign->character->class }}, Lv. {{ $campaign->character->level }})</span>
            <span>&bull;</span>
            <span>Lokasi: <strong>{{ $campaign->current_location ?? 'Tidak Diketahui' }}</strong></span>
            <span>&bull;</span>
            <span>Status: <strong style="color: {{ $campaign->status === 'Active' ? '#10b981' : '#60a5fa' }};">{{ $campaign->status }}</strong></span>
        </div>

        @if ($campaign->description)
            <p style="font-size: 0.9rem; color: #94a3b8; line-height: 1.5; font-style: italic; border-top: 1px solid #242938; padding-top: 12px;">
                "{{ $campaign->description }}"
            </p>
        @endif
    </div>

    <!-- Chronicle Story Log -->
    <div style="margin-bottom: 30px;">
        <h2 class="font-display" style="font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-book-open" style="color: var(--gold-bright);"></i>
            <span>Lembaran Catatan Petualangan (Chronicle)</span>
        </h2>

        @if ($campaign->storyHistories->isEmpty())
            <div class="rpg-card" style="padding: 30px; text-align: center; color: #94a3b8;">
                Belum ada riwayat cerita tercatat.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach ($campaign->storyHistories as $index => $history)
                    <div class="rpg-card" style="padding: 24px; position: relative;">
                        <!-- Step counter -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #202636; padding-bottom: 8px;">
                            <span class="font-display" style="font-size: 0.85rem; font-weight: 700; color: var(--gold-bright);">
                                Babak {{ $index + 1 }}
                            </span>
                            <span style="font-size: 0.78rem; color: #64748b;">
                                {{ $history->created_at->format('H:i, d M Y') }}
                            </span>
                        </div>

                        <!-- Player Action (If present) -->
                        @if ($history->player_action)
                            <div style="background: rgba(30, 41, 59, 0.4); border-left: 3px solid #3b82f6; border-radius: 0 6px 6px 0; padding: 10px 14px; margin-bottom: 16px;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #60a5fa; text-transform: uppercase; margin-bottom: 2px;">
                                    <i class="fa-solid fa-user-ninja"></i> Tindakan Pemain:
                                </div>
                                <div style="font-size: 0.92rem; color: #e2e8f0; font-style: italic;">
                                    "{{ $history->player_action }}"
                                </div>
                            </div>
                        @endif

                        <!-- DM Narrative -->
                        <div style="font-size: 0.95rem; line-height: 1.6; color: #f1f5f9; font-family: var(--font-body); margin-bottom: 14px;">
                            {!! nl2br(e($history->narrative)) !!}
                        </div>

                        <!-- Outcome Footer & Mechanics -->
                        @if ($history->reasoning || $history->action_result)
                            <div style="background: #090c12; border: 1px solid #1c212e; border-radius: 6px; padding: 10px 12px; font-size: 0.8rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                <div style="color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-dice-d20" style="color: var(--gold-bright);"></i>
                                    <span>{{ $history->reasoning ?? 'Evaluasi tindakan D&D' }}</span>
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center;">
                                    @if ($history->hp_change != 0)
                                        <span style="color: {{ $history->hp_change > 0 ? '#10b981' : '#ef4444' }}; font-weight: 700;">
                                            <i class="fa-solid fa-heart"></i> {{ ($history->hp_change > 0 ? '+' : '') . $history->hp_change }} HP
                                        </span>
                                    @endif
                                    @if ($history->xp_change > 0)
                                        <span style="color: #f59e0b; font-weight: 700;">
                                            <i class="fa-solid fa-star"></i> +{{ $history->xp_change }} XP
                                        </span>
                                    @endif
                                    @if ($history->action_result)
                                        @php
                                            $resultBadgeColors = [
                                                'critical_success' => ['bg' => 'rgba(16, 185, 129, 0.2)', 'border' => '#10b981', 'text' => '#34d399', 'label' => 'Critical Success!'],
                                                'success' => ['bg' => 'rgba(16, 185, 129, 0.15)', 'border' => '#059669', 'text' => '#10b981', 'label' => 'Berhasil'],
                                                'partial_success' => ['bg' => 'rgba(245, 158, 11, 0.2)', 'border' => '#f59e0b', 'text' => '#fbbf24', 'label' => 'Berhasil Sebagian'],
                                                'failure' => ['bg' => 'rgba(220, 38, 38, 0.2)', 'border' => '#ef4444', 'text' => '#f87171', 'label' => 'Gagal'],
                                                'critical_failure' => ['bg' => 'rgba(153, 27, 27, 0.3)', 'border' => '#dc2626', 'text' => '#fca5a5', 'label' => 'Critical Failure!'],
                                                'info' => ['bg' => 'rgba(59, 130, 246, 0.15)', 'border' => '#3b82f6', 'text' => '#60a5fa', 'label' => 'Prolog / Informasi'],
                                            ];
                                            $b = $resultBadgeColors[$history->action_result] ?? $resultBadgeColors['info'];
                                        @endphp
                                        <span style="background: {{ $b['bg'] }}; border: 1px solid {{ $b['border'] }}; color: {{ $b['text'] }}; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                                            {{ $b['label'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
