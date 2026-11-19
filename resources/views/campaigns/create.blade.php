@extends('layouts.app')

@section('title', 'Mulai Petualangan Baru')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 28px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 18px;">
        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-map-location-dot" style="color: var(--gold-bright);"></i>
            <span>Mulai Campaign Petualangan Baru</span>
        </h1>
        <p style="color: #94a3b8; font-size: 0.92rem; margin-top: 4px;">
            Pilih karakter andalanmu, tetapkan judul ekspedisi, dan bersiaplah menghadapi ujian takdir dari AI Dungeon Master.
        </p>
    </div>

    @if ($characters->isEmpty())
        <div class="rpg-card" style="padding: 40px 24px; text-align: center;">
            <i class="fa-solid fa-shield-virus" style="font-size: 2.5rem; color: #f59e0b; margin-bottom: 14px;"></i>
            <h3 class="font-display" style="font-size: 1.25rem; color: #fff; margin-bottom: 8px;">Anda Belum Memiliki Karakter</h3>
            <p style="color: #94a3b8; font-size: 0.9rem; max-width: 460px; margin: 0 auto 20px;">
                Untuk memulai campaign petualangan, Anda harus menciptakan setidaknya satu karakter petualang terlebih dahulu.
            </p>
            <a href="{{ route('characters.create') }}" class="btn-rpg btn-rpg-gold">
                <i class="fa-solid fa-user-plus"></i> Ciptakan Karakter Sekarang
            </a>
        </div>
    @else
        <div class="rpg-card" style="padding: 30px;">
            <form action="{{ route('campaigns.store') }}" method="POST">
                @csrf

                <!-- Select Character -->
                <div class="form-group">
                    <label for="character_id" class="form-label">
                        <i class="fa-solid fa-user-shield" style="color: var(--gold-primary); margin-right: 4px;"></i> 1. Pilih Karakter Petualang
                    </label>
                    <select id="character_id" name="character_id" class="form-select" required>
                        <option value="">-- Pilih Karakter yang akan Menjelajah --</option>
                        @foreach ($characters as $char)
                            <option value="{{ $char->id }}" {{ (old('character_id', request('character_id')) == $char->id) ? 'selected' : '' }}>
                                {{ $char->name }} ({{ $char->race }} {{ $char->class }}, Level {{ $char->level }}, {{ $char->current_hp }}/{{ $char->max_hp }} HP)
                            </option>
                        @endforeach
                    </select>
                    @error('character_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campaign Title -->
                <div class="form-group">
                    <label for="title" class="form-label">
                        <i class="fa-solid fa-feather-pointed" style="color: var(--gold-primary); margin-right: 4px;"></i> 2. Judul Campaign / Ekspedisi
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-input" placeholder="Contoh: Menyelidiki Gua Terkutuk Ravenloft" required maxlength="150">
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Difficulty Selector -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-skull" style="color: var(--gold-primary); margin-right: 4px;"></i> 3. Tingkat Kesulitan (Difficulty Class)
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 10px;">
                        @php
                            $diffs = [
                                'Easy' => ['desc' => 'Tantangan ringan (DC 10), monster ramah, cocok untuk pemula.', 'color' => '#10b981', 'icon' => 'fa-feather'],
                                'Medium' => ['desc' => 'Standar D&D (DC 13), musuh seimbang, risiko nyata.', 'color' => '#f59e0b', 'icon' => 'fa-shield-halved'],
                                'Hard' => ['desc' => 'Tantangan berat (DC 16), jebakan mematikan, monster cerdas.', 'color' => '#ef4444', 'icon' => 'fa-skull'],
                                'Legendary' => ['desc' => 'Ujian neraka (DC 19), ancaman kematian permanen dan bos epik.', 'color' => '#a855f7', 'icon' => 'fa-dragon'],
                            ];
                        @endphp

                        @foreach ($diffs as $dName => $dInfo)
                            <label style="cursor: pointer;">
                                <input type="radio" name="difficulty" value="{{ $dName }}" {{ (old('difficulty', 'Medium') === $dName) ? 'checked' : '' }} style="display: none;" class="diff-radio">
                                <div class="diff-card" style="background: #0f121a; border: 1px solid #242938; border-radius: 8px; padding: 14px 10px; text-align: center; transition: all 0.2s ease;">
                                    <div style="font-size: 1.3rem; color: {{ $dInfo['color'] }}; margin-bottom: 6px;">
                                        <i class="fa-solid {{ $dInfo['icon'] }}"></i>
                                    </div>
                                    <div class="font-display" style="font-size: 0.95rem; font-weight: 700; color: #fff;">{{ $dName }}</div>
                                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 4px; line-height: 1.3;">
                                        {{ $dInfo['desc'] }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('difficulty')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label for="description" class="form-label">
                        <i class="fa-solid fa-scroll" style="color: var(--gold-primary); margin-right: 4px;"></i> 4. Catatan Misi / Premis Cerita (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3" class="form-textarea" placeholder="Tuliskan latar belakang singkat misi, rumor lokal yang kamu dengar di kedai tavern, atau tujuan yang ingin kamu capai...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px;">
                    <a href="{{ route('campaigns.index') }}" class="btn-rpg btn-rpg-dark">Batal</a>
                    <button type="submit" class="btn-rpg btn-rpg-gold" style="padding: 12px 24px;">
                        <i class="fa-solid fa-dice-d20"></i> Mulai Petualangan Bersama AI DM
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@section('scripts')
<script>
    function updateDiffCards() {
        document.querySelectorAll('.diff-radio').forEach(r => {
            const card = r.nextElementSibling;
            if (r.checked) {
                card.style.borderColor = 'var(--gold-primary)';
                card.style.background = 'rgba(212, 175, 55, 0.12)';
                card.style.boxShadow = '0 0 12px rgba(212, 175, 55, 0.3)';
            } else {
                card.style.borderColor = '#242938';
                card.style.background = '#0f121a';
                card.style.boxShadow = 'none';
            }
        });
    }

    document.querySelectorAll('.diff-radio').forEach(r => {
        r.addEventListener('change', updateDiffCards);
    });
    updateDiffCards();
</script>
@endsection
@endsection
