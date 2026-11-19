@props([
    'name' => 'STR',
    'fullName' => 'Strength',
    'score' => 10,
    'modifier' => '+0',
    'icon' => 'fa-hand-fist'
])

<div style="background: #0f121a; border: 1px solid #242938; border-radius: 8px; padding: 10px; text-align: center; position: relative; overflow: hidden;">
    <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em; display: flex; align-items: center; justify-content: center; gap: 4px;">
        <i class="fa-solid {{ $icon }}" style="color: var(--gold-primary); font-size: 0.75rem;"></i>
        <span>{{ $name }}</span>
    </div>
    <div style="font-family: var(--font-display); font-size: 1.45rem; font-weight: 800; color: #fff; margin: 2px 0;">
        {{ $score }}
    </div>
    <div style="display: inline-block; background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.4); color: var(--gold-bright); font-size: 0.78rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; letter-spacing: 0.04em;">
        {{ $modifier }}
    </div>
</div>
