@extends('layouts.app')

@section('title', 'Ciptakan Karakter Baru')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="margin-bottom: 28px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 18px;">
        <h1 class="font-display" style="font-size: 1.8rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 12px;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--gold-bright);"></i>
            <span>Penciptaan Karakter Petualang</span>
        </h1>
        <p style="color: #94a3b8; font-size: 0.92rem; margin-top: 4px;">
            Tentukan ras leluhur, kelas tempur, dan statistik dasar yang akan menentukan takdirmu di hadapan Dungeon Master.
        </p>
    </div>

    <form action="{{ route('characters.store') }}" method="POST" id="characterForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 28px; align-items: flex-start;">
            <!-- Left Column: Form Controls -->
            <div>
                <!-- Character Name & Backstory -->
                <div class="rpg-card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 16px;">
                        <i class="fa-solid fa-id-card"></i> 1. Identitas & Latar Belakang
                    </h3>

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Karakter</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input" placeholder="Contoh: Valerius Sang Penjaga Gerbang" required maxlength="60">
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="backstory" class="form-label">Latar Belakang Cerita (Backstory - Opsional)</label>
                        <textarea id="backstory" name="backstory" rows="3" class="form-textarea" placeholder="Ceritakan asal-usul, motivasi, atau sumpah yang dipegang karaktermu...">{{ old('backstory') }}</textarea>
                        @error('backstory')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Class Selection -->
                <div class="rpg-card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 6px;">
                        <i class="fa-solid fa-shield-halved"></i> 2. Pilih Kelas Karakter
                    </h3>
                    <p style="color: #64748b; font-size: 0.82rem; margin-bottom: 16px;">
                        Setiap kelas memiliki statistik utama, dadu nyawa (Hit Die), dan perlengkapan khusus.
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px;">
                        @foreach ($classes as $className => $classData)
                            <label class="class-picker-option" style="cursor: pointer;">
                                <input type="radio" name="class" value="{{ $className }}" {{ (old('class', 'Warrior') === $className) ? 'checked' : '' }} style="display: none;">
                                <div class="picker-box" data-class="{{ $className }}" style="background: #0f121a; border: 1px solid #242938; border-radius: 8px; padding: 14px 10px; text-align: center; transition: all 0.2s ease;">
                                    <div style="font-size: 1.4rem; color: var(--gold-bright); margin-bottom: 6px;">
                                        @switch($className)
                                            @case('Warrior') <i class="fa-solid fa-shield-halved"></i> @break
                                            @case('Mage') <i class="fa-solid fa-wand-magic-sparkles"></i> @break
                                            @case('Archer') <i class="fa-solid fa-bow-arrow"></i> @break
                                            @case('Rogue') <i class="fa-solid fa-dagger"></i> @break
                                            @case('Cleric') <i class="fa-solid fa-cross"></i> @break
                                            @default <i class="fa-solid fa-user"></i>
                                        @endswitch
                                    </div>
                                    <div class="font-display" style="font-size: 0.95rem; font-weight: 700; color: #fff;">{{ $className }}</div>
                                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 2px;">d{{ $classData['hit_die'] }} HP</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('class')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Race Selection -->
                <div class="rpg-card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 class="font-display" style="font-size: 1.15rem; color: var(--gold-bright); margin-bottom: 6px;">
                        <i class="fa-solid fa-dna"></i> 3. Pilih Ras Leluhur
                    </h3>
                    <p style="color: #64748b; font-size: 0.82rem; margin-bottom: 16px;">
                        Ras memberikan bonus atribut khusus sesuai fisiologi dan warisan budaya mereka.
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px;">
                        @foreach ($races as $raceName => $raceData)
                            <label class="race-picker-option" style="cursor: pointer;">
                                <input type="radio" name="race" value="{{ $raceName }}" {{ (old('race', 'Human') === $raceName) ? 'checked' : '' }} style="display: none;">
                                <div class="race-box" data-race="{{ $raceName }}" style="background: #0f121a; border: 1px solid #242938; border-radius: 8px; padding: 12px 8px; text-align: center; transition: all 0.2s ease;">
                                    <div class="font-display" style="font-size: 0.88rem; font-weight: 700; color: #fff;">{{ $raceName }}</div>
                                    <div style="font-size: 0.68rem; color: var(--gold-primary); margin-top: 4px;">
                                        @php
                                            $bonusParts = [];
                                            foreach($raceData['bonuses'] as $bStat => $bVal) {
                                                $bonusParts[] = '+' . $bVal . ' ' . strtoupper(substr($bStat, 0, 3));
                                            }
                                        @endphp
                                        {{ implode(', ', array_slice($bonusParts, 0, 2)) }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('race')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Right Column: Live Attribute & Preview Panel -->
            <div style="position: sticky; top: 92px;">
                <div class="rpg-card gold-border" style="padding: 24px; background: #11141e;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px solid rgba(212, 175, 55, 0.2); padding-bottom: 10px;">
                        <h3 class="font-display" style="font-size: 1.15rem; color: #fff; font-weight: 700;">
                            <i class="fa-solid fa-chart-simple" style="color: var(--gold-bright); margin-right: 6px;"></i> Pratinjau Karakter
                        </h3>
                        <span id="previewTag" style="background: rgba(212, 175, 55, 0.2); color: var(--gold-bright); font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 12px;">
                            Level 1
                        </span>
                    </div>

                    <!-- Selected Summary -->
                    <div style="margin-bottom: 16px; background: #0a0c12; padding: 12px; border-radius: 8px; border: 1px solid #1c212e;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #94a3b8; font-size: 0.85rem;">Kombinasi:</span>
                            <span id="previewCombo" style="font-weight: 700; color: #fff; font-size: 0.95rem;">Human Warrior</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                            <span style="color: #94a3b8; font-size: 0.85rem;">Maksimal HP Awal:</span>
                            <span id="previewHp" style="font-weight: 800; color: #10b981; font-size: 1.05rem;">15 HP</span>
                        </div>
                    </div>

                    <!-- 6 Attributes Live Calculation Grid -->
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px;">
                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">STR</div>
                            <div id="statStr" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">16</div>
                            <div id="modStr" style="font-size: 0.72rem; color: var(--gold-bright); font-weight: 700;">+3</div>
                        </div>

                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">DEX</div>
                            <div id="statDex" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">12</div>
                            <div id="modDex" style="font-size: 0.72rem; color: var(--gold-bright); font-weight: 700;">+1</div>
                        </div>

                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">CON</div>
                            <div id="statCon" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">14</div>
                            <div id="modCon" style="font-size: 0.72rem; color: var(--gold-bright); font-weight: 700;">+2</div>
                        </div>

                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">INT</div>
                            <div id="statInt" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">8</div>
                            <div id="modInt" style="font-size: 0.72rem; color: #94a3b8; font-weight: 700;">-1</div>
                        </div>

                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">WIS</div>
                            <div id="statWis" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">10</div>
                            <div id="modWis" style="font-size: 0.72rem; color: #cbd5e1; font-weight: 700;">+0</div>
                        </div>

                        <div class="stat-calc-box" style="background: #090b10; border: 1px solid #222736; border-radius: 6px; padding: 8px; text-align: center;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700;">CHA</div>
                            <div id="statCha" class="font-display" style="font-size: 1.25rem; font-weight: 800; color: #fff;">10</div>
                            <div id="modCha" style="font-size: 0.72rem; color: #cbd5e1; font-weight: 700;">+0</div>
                        </div>
                    </div>

                    <!-- Class Lore Info -->
                    <div id="classLore" style="font-size: 0.8rem; color: #94a3b8; line-height: 1.4; margin-bottom: 20px; background: rgba(0,0,0,0.25); padding: 12px; border-radius: 6px; border-left: 3px solid var(--gold-bright);">
                        Petarung tangguh bersenjatakan pedang dan perisai, ahli pertempuran jarak dekat.
                    </div>

                    <button type="submit" class="btn-rpg btn-rpg-gold" style="width: 100%; padding: 14px; font-size: 0.95rem;">
                        <i class="fa-solid fa-scroll"></i> Ciptakan Karakter Ini
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
    const classData = @json($classes);
    const raceData = @json($races);

    function calculateModifier(val) {
        const mod = Math.floor((val - 10) / 2);
        return (mod >= 0 ? '+' : '') + mod;
    }

    function updatePreview() {
        const selectedClassInput = document.querySelector('input[name="class"]:checked');
        const selectedRaceInput = document.querySelector('input[name="race"]:checked');

        if (!selectedClassInput || !selectedRaceInput) return;

        const className = selectedClassInput.value;
        const raceName = selectedRaceInput.value;

        // Visual selection state updates
        document.querySelectorAll('.picker-box').forEach(box => {
            if (box.dataset.class === className) {
                box.style.borderColor = 'var(--gold-primary)';
                box.style.background = 'rgba(212, 175, 55, 0.12)';
                box.style.boxShadow = '0 0 12px rgba(212, 175, 55, 0.3)';
            } else {
                box.style.borderColor = '#242938';
                box.style.background = '#0f121a';
                box.style.boxShadow = 'none';
            }
        });

        document.querySelectorAll('.race-box').forEach(box => {
            if (box.dataset.race === raceName) {
                box.style.borderColor = 'var(--gold-primary)';
                box.style.background = 'rgba(212, 175, 55, 0.12)';
                box.style.boxShadow = '0 0 12px rgba(212, 175, 55, 0.3)';
            } else {
                box.style.borderColor = '#242938';
                box.style.background = '#0f121a';
                box.style.boxShadow = 'none';
            }
        });

        const cInfo = classData[className];
        const rInfo = raceData[raceName];

        let stats = { ...cInfo.stats };
        for (const [s, bonus] of Object.entries(rInfo.bonuses)) {
            if (stats[s] !== undefined) {
                stats[s] = Math.min(20, stats[s] + bonus);
            }
        }

        const conMod = Math.floor((stats.constitution - 10) / 2);
        const maxHp = Math.max(6, cInfo.base_hp + conMod);

        document.getElementById('previewCombo').textContent = `${raceName} ${className}`;
        document.getElementById('previewHp').textContent = `${maxHp} HP`;
        document.getElementById('classLore').textContent = cInfo.description;

        // Update 6 stats
        const statKeys = ['strength', 'dexterity', 'constitution', 'intelligence', 'wisdom', 'charisma'];
        statKeys.forEach(key => {
            const shortKey = key.substring(0, 3).charAt(0).toUpperCase() + key.substring(1, 3);
            const val = stats[key];
            const mod = calculateModifier(val);

            const statEl = document.getElementById('stat' + shortKey);
            const modEl = document.getElementById('mod' + shortKey);

            if (statEl) statEl.textContent = val;
            if (modEl) {
                modEl.textContent = mod;
                modEl.style.color = mod.startsWith('+') && mod !== '+0' ? 'var(--gold-bright)' : (mod.startsWith('-') ? '#f87171' : '#cbd5e1');
            }
        });
    }

    document.querySelectorAll('input[name="class"]').forEach(input => {
        input.addEventListener('change', updatePreview);
    });
    document.querySelectorAll('input[name="race"]').forEach(input => {
        input.addEventListener('change', updatePreview);
    });

    // Run initial on mount
    updatePreview();
</script>
@endsection
@endsection
