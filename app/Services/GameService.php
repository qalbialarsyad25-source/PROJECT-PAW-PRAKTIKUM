<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Character;
use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GameService
{
    /**
     * Preset configurations for character classes.
     */
    public const CLASSES = [
        'Warrior' => [
            'description' => 'Petarung tangguh bersenjatakan pedang dan perisai, ahli pertempuran jarak dekat.',
            'hit_die' => 10,
            'base_hp' => 14,
            'stats' => [
                'strength' => 16,
                'dexterity' => 12,
                'constitution' => 14,
                'intelligence' => 8,
                'wisdom' => 10,
                'charisma' => 10,
            ],
            'starter_items' => [
                ['name' => 'Pedang Panjang Baja', 'category' => 'weapon', 'effect_type' => 'damage', 'effect_value' => 8, 'description' => 'Pedang tempaan pandai besi kerajaan, memberikan tebasan mematikan.', 'icon' => 'fa-sword', 'equipped' => true, 'qty' => 1],
                ['name' => 'Perisai Besi Tempa', 'category' => 'armor', 'effect_type' => 'buff_ac', 'effect_value' => 2, 'description' => 'Perisai kokoh penahan hantaman senjata tumpul maupun tajam.', 'icon' => 'fa-shield-halved', 'equipped' => true, 'qty' => 1],
                ['name' => 'Ramuan Penyembuh (Health Potion)', 'category' => 'potion', 'effect_type' => 'heal', 'effect_value' => 15, 'description' => 'Cairan merah berkilau yang memulihkan 15 HP luka fisik.', 'icon' => 'fa-flask-round-potion', 'equipped' => false, 'qty' => 2],
            ],
        ],
        'Mage' => [
            'description' => 'Penguasa energi magis kuno, mampu mengendalikan elemen dan mantra misterius.',
            'hit_die' => 6,
            'base_hp' => 10,
            'stats' => [
                'strength' => 8,
                'dexterity' => 12,
                'constitution' => 11,
                'intelligence' => 16,
                'wisdom' => 13,
                'charisma' => 10,
            ],
            'starter_items' => [
                ['name' => 'Tongkat Kayu Oak Bertuah', 'category' => 'weapon', 'effect_type' => 'damage', 'effect_value' => 6, 'description' => 'Fokus sihir bertatahkan kristal biru di ujungnya.', 'icon' => 'fa-wand-magic-sparkles', 'equipped' => true, 'qty' => 1],
                ['name' => 'Jubah Pelindung Arcane', 'category' => 'armor', 'effect_type' => 'buff_ac', 'effect_value' => 1, 'description' => 'Jubah sutra tenunan penyihir yang meredam serangan elemen.', 'icon' => 'fa-vest', 'equipped' => true, 'qty' => 1],
                ['name' => 'Ramuan Penyembuh (Health Potion)', 'category' => 'potion', 'effect_type' => 'heal', 'effect_value' => 15, 'description' => 'Cairan merah berkilau yang memulihkan 15 HP.', 'icon' => 'fa-flask-round-potion', 'equipped' => false, 'qty' => 2],
            ],
        ],
        'Archer' => [
            'description' => 'Penembak jitu yang menguasai busur, membaca jejak alam, dan memiliki ketangkasan tinggi.',
            'hit_die' => 8,
            'base_hp' => 12,
            'stats' => [
                'strength' => 10,
                'dexterity' => 16,
                'constitution' => 12,
                'intelligence' => 9,
                'wisdom' => 14,
                'charisma' => 9,
            ],
            'starter_items' => [
                ['name' => 'Busur Panjang Kayu Yew', 'category' => 'weapon', 'effect_type' => 'damage', 'effect_value' => 7, 'description' => 'Busur fleksibel dengan jangkauan tembak yang akurat.', 'icon' => 'fa-bow-arrow', 'equipped' => true, 'qty' => 1],
                ['name' => 'Armor Kulit Rusa', 'category' => 'armor', 'effect_type' => 'buff_ac', 'effect_value' => 2, 'description' => 'Pelindung ringan yang tidak menghambat pergerakan akrobatik.', 'icon' => 'fa-shirt', 'equipped' => true, 'qty' => 1],
                ['name' => 'Ramuan Penyembuh (Health Potion)', 'category' => 'potion', 'effect_type' => 'heal', 'effect_value' => 15, 'description' => 'Cairan penyembuh herbal segar yang memulihkan 15 HP.', 'icon' => 'fa-flask-round-potion', 'equipped' => false, 'qty' => 2],
            ],
        ],
        'Rogue' => [
            'description' => 'Spesialis bayangan, ahli melucuti jebakan, mengendap-endap, dan serangan mendadak mematikan.',
            'hit_die' => 8,
            'base_hp' => 11,
            'stats' => [
                'strength' => 9,
                'dexterity' => 16,
                'constitution' => 12,
                'intelligence' => 11,
                'wisdom' => 10,
                'charisma' => 12,
            ],
            'starter_items' => [
                ['name' => 'Sepasang Belati Baja Hitam', 'category' => 'weapon', 'effect_type' => 'damage', 'effect_value' => 6, 'description' => 'Belati tajam tanpa pantulan cahaya untuk serangan senyap.', 'icon' => 'fa-dagger', 'equipped' => true, 'qty' => 1],
                ['name' => 'Pakaian Kamuflase Malam', 'category' => 'armor', 'effect_type' => 'buff_ac', 'effect_value' => 1, 'description' => 'Pakaian hitam berperedam suara langkah kaki.', 'icon' => 'fa-mask', 'equipped' => true, 'qty' => 1],
                ['name' => 'Ramuan Penyembuh (Health Potion)', 'category' => 'potion', 'effect_type' => 'heal', 'effect_value' => 15, 'description' => 'Ramuan pemulih vitalitas saat genting.', 'icon' => 'fa-flask-round-potion', 'equipped' => false, 'qty' => 2],
            ],
        ],
        'Cleric' => [
            'description' => 'Ksatria suci pelindung cahaya, menyembuhkan rekan, dan menghalau kejahatan mistis.',
            'hit_die' => 8,
            'base_hp' => 13,
            'stats' => [
                'strength' => 13,
                'dexterity' => 9,
                'constitution' => 14,
                'intelligence' => 9,
                'wisdom' => 16,
                'charisma' => 11,
            ],
            'starter_items' => [
                ['name' => 'Gada Suci Fajar', 'category' => 'weapon', 'effect_type' => 'damage', 'effect_value' => 7, 'description' => 'Senjata tumpul bermotif matahari suci yang diberkati doa.', 'icon' => 'fa-hammer', 'equipped' => true, 'qty' => 1],
                ['name' => 'Baju Zirah Rantai Suci', 'category' => 'armor', 'effect_type' => 'buff_ac', 'effect_value' => 3, 'description' => 'Zirah rantai berkat pelindung jiwa dan raga.', 'icon' => 'fa-shield', 'equipped' => true, 'qty' => 1],
                ['name' => 'Ramuan Penyembuh Berkat (Holy Potion)', 'category' => 'potion', 'effect_type' => 'heal', 'effect_value' => 20, 'description' => 'Air suci yang mengembalikan 20 HP dan menyegarkan jiwa.', 'icon' => 'fa-flask-round-potion', 'equipped' => false, 'qty' => 2],
            ],
        ],
    ];

    /**
     * Preset race modifiers and bonuses.
     */
    public const RACES = [
        'Human' => [
            'description' => 'Adaptif dan pantang menyerah. Memiliki peningkatan merata pada semua atribut.',
            'bonuses' => ['strength' => 1, 'dexterity' => 1, 'constitution' => 1, 'intelligence' => 1, 'wisdom' => 1, 'charisma' => 1],
        ],
        'Elf' => [
            'description' => 'Anggun, berumur panjang, memiliki penglihatan malam dan kelincahan alami.',
            'bonuses' => ['dexterity' => 2, 'intelligence' => 1],
        ],
        'Dwarf' => [
            'description' => 'Kuat, ulet, tahan terhadap racun dan pukulan fisik yang berat.',
            'bonuses' => ['constitution' => 2, 'strength' => 1],
        ],
        'Halfling' => [
            'description' => 'Bertubuh mungil, ceria, memiliki keberuntungan tinggi dan langkah tanpa suara.',
            'bonuses' => ['dexterity' => 2, 'charisma' => 1],
        ],
        'Dragonborn' => [
            'description' => 'Keturunan naga yang perkasa dengan nafas elemental dan aura wibawa.',
            'bonuses' => ['strength' => 2, 'charisma' => 1],
        ],
        'Half-Orc' => [
            'description' => 'Petarung liar yang memiliki daya tahan luar biasa dan serangan brutal.',
            'bonuses' => ['strength' => 2, 'constitution' => 1],
        ],
        'Tiefling' => [
            'description' => 'Mewarisi garis keturunan infernal, memiliki karisma gelap dan kecerdasan mistis.',
            'bonuses' => ['charisma' => 2, 'intelligence' => 1],
        ],
    ];

    /**
     * Calculate initial attributes based on Class and Race.
     */
    public function calculateInitialStats(string $class, string $race): array
    {
        $classData = self::CLASSES[$class] ?? self::CLASSES['Warrior'];
        $raceData = self::RACES[$race] ?? self::RACES['Human'];

        $stats = $classData['stats'];

        foreach ($raceData['bonuses'] as $stat => $bonus) {
            if (isset($stats[$stat])) {
                $stats[$stat] = min(20, $stats[$stat] + $bonus);
            }
        }

        $conMod = (int) floor(($stats['constitution'] - 10) / 2);
        $maxHp = max(6, $classData['base_hp'] + $conMod);

        return [
            'stats' => $stats,
            'max_hp' => $maxHp,
            'current_hp' => $maxHp,
        ];
    }

    /**
     * Equip starter items for character and optionally for a specific campaign.
     */
    public function grantStarterItems(Character $character, ?Campaign $campaign = null): void
    {
        $classData = self::CLASSES[$character->class] ?? self::CLASSES['Warrior'];

        foreach ($classData['starter_items'] as $itemData) {
            $slug = Str::slug($itemData['name']);

            $item = Item::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $itemData['name'],
                    'description' => $itemData['description'],
                    'category' => $itemData['category'],
                    'effect_type' => $itemData['effect_type'] ?? null,
                    'effect_value' => $itemData['effect_value'] ?? null,
                    'icon' => $itemData['icon'] ?? 'fa-box',
                    'is_default' => true,
                ]
            );

            // Create inventory scoped to campaign or base character
            Inventory::firstOrCreate(
                [
                    'campaign_id' => $campaign?->id,
                    'character_id' => $character->id,
                    'item_id' => $item->id,
                ],
                [
                    'quantity' => $itemData['qty'] ?? 1,
                    'is_equipped' => $itemData['equipped'] ?? false,
                ]
            );
        }
    }

    /**
     * Apply validated gameplay updates to campaign-isolated character status and inventory.
     * Guaranteed to run inside a database transaction.
     *
     * @param Campaign $campaign
     * @param array $aiOutcome Validated outcome from Gemini or Game Engine
     * @return array Summary of applied game state modifications
     */
    public function applyActionOutcome(Campaign $campaign, array $aiOutcome): array
    {
        return DB::transaction(function () use ($campaign, $aiOutcome) {
            $character = $campaign->character;
            $hpChange = (int) ($aiOutcome['hp_change'] ?? 0);
            $xpChange = (int) ($aiOutcome['xp_change'] ?? 0);

            // Use campaign-isolated HP values
            $currentMaxHp = $campaign->max_hp ?: $character->max_hp;
            $oldHp = $campaign->current_hp ?? $currentMaxHp;

            $newHp = max(0, min($currentMaxHp, $oldHp + $hpChange));
            $actualHpDelta = $newHp - $oldHp;

            $campaign->current_hp = $newHp;
            $campaign->max_hp = $currentMaxHp;

            // Character status update in this campaign
            if ($newHp <= 0) {
                $campaign->character_status = 'Unconscious';
            } elseif ($campaign->character_status === 'Unconscious' && $newHp > 0) {
                $campaign->character_status = 'Alive';
            }

            // Cap and validate XP change
            $xpGain = max(0, $xpChange);
            $campaign->experience = ($campaign->experience ?? 0) + $xpGain;

            // Check level up inside this campaign
            $didLevelUp = false;
            $currentLevel = $campaign->level ?: 1;

            while ($campaign->experience >= $campaign->xpThresholdForNextLevel() && $currentLevel < 20) {
                $currentLevel += 1;
                $campaign->level = $currentLevel;
                $didLevelUp = true;

                // HP bonus per level (hit die average + CON mod)
                $classData = self::CLASSES[$character->class] ?? self::CLASSES['Warrior'];
                $hitDie = $classData['hit_die'] ?? 8;
                $conMod = $character->getModifier('constitution');
                $hpIncrease = max(1, (int) round($hitDie / 2) + 1 + $conMod);

                $campaign->max_hp += $hpIncrease;
                $campaign->current_hp = $campaign->max_hp; // fully restore on level up
            }

            $campaign->save();

            // Process campaign-isolated inventory modifications
            $processedItems = [];
            if (!empty($aiOutcome['inventory_changes']) && is_array($aiOutcome['inventory_changes'])) {
                foreach ($aiOutcome['inventory_changes'] as $change) {
                    $action = strtolower($change['action'] ?? 'add');
                    $itemName = trim($change['name'] ?? $change['item_name'] ?? '');

                    if (empty($itemName)) {
                        continue;
                    }

                    if ($action === 'add') {
                        $category = $change['category'] ?? 'equipment';
                        $desc = $change['description'] ?? "Item ditemukan dalam petualangan: {$itemName}.";
                        $effectType = $change['effect_type'] ?? null;
                        $effectValue = isset($change['effect_value']) ? (int) $change['effect_value'] : null;
                        $qty = max(1, (int) ($change['quantity'] ?? 1));

                        $slug = Str::slug($itemName);
                        $item = Item::firstOrCreate(
                            ['slug' => $slug],
                            [
                                'name' => $itemName,
                                'description' => $desc,
                                'category' => in_array($category, ['weapon', 'armor', 'potion', 'equipment', 'quest']) ? $category : 'equipment',
                                'effect_type' => $effectType,
                                'effect_value' => $effectValue,
                                'icon' => $this->guessItemIcon($category),
                            ]
                        );

                        $inv = Inventory::firstOrNew([
                            'campaign_id' => $campaign->id,
                            'character_id' => $character->id,
                            'item_id' => $item->id,
                        ]);

                        $inv->quantity = ($inv->quantity ?? 0) + $qty;
                        $inv->save();

                        $processedItems[] = [
                            'action' => 'added',
                            'name' => $item->name,
                            'quantity' => $qty,
                        ];
                    } elseif ($action === 'remove') {
                        $qty = max(1, (int) ($change['quantity'] ?? 1));
                        $item = Item::where('name', 'like', "%{$itemName}%")->first();

                        if ($item) {
                            $inv = Inventory::where('campaign_id', $campaign->id)
                                ->where('character_id', $character->id)
                                ->where('item_id', $item->id)
                                ->first();

                            if ($inv) {
                                if ($inv->quantity <= $qty) {
                                    $inv->delete();
                                } else {
                                    $inv->quantity -= $qty;
                                    $inv->save();
                                }

                                $processedItems[] = [
                                    'action' => 'removed',
                                    'name' => $item->name,
                                    'quantity' => $qty,
                                ];
                            }
                        }
                    }
                }
            }

            return [
                'old_hp' => $oldHp,
                'new_hp' => $campaign->current_hp,
                'hp_delta' => $actualHpDelta,
                'old_xp' => $campaign->experience - $xpGain,
                'new_xp' => $campaign->experience,
                'xp_gain' => $xpGain,
                'did_level_up' => $didLevelUp,
                'new_level' => $campaign->level,
                'status' => $campaign->character_status,
                'inventory_changes' => $processedItems,
            ];
        });
    }

    /**
     * Use a consumable item in a campaign (e.g., Potion of Healing).
     */
    public function useItem(Campaign $campaign, int $itemId): array
    {
        return DB::transaction(function () use ($campaign, $itemId) {
            $inventory = Inventory::with('item')
                ->where('campaign_id', $campaign->id)
                ->where('item_id', $itemId)
                ->first();

            // Fallback to base character inventory if campaign-specific doesn't exist
            if (!$inventory) {
                $inventory = Inventory::with('item')
                    ->whereNull('campaign_id')
                    ->where('character_id', $campaign->character_id)
                    ->where('item_id', $itemId)
                    ->first();
            }

            if (!$inventory || $inventory->quantity < 1) {
                return [
                    'success' => false,
                    'message' => 'Item tidak ditemukan di inventaris campaign ini.',
                ];
            }

            $item = $inventory->item;
            $maxHp = $campaign->max_hp ?: $campaign->character->max_hp;
            $currentHp = $campaign->current_hp ?? $maxHp;

            // Handle healing item
            if ($item->category === 'potion' || $item->effect_type === 'heal') {
                $healAmount = $item->effect_value ?: 15;
                $newHp = min($maxHp, $currentHp + $healAmount);
                $restored = $newHp - $currentHp;

                $campaign->current_hp = $newHp;

                if ($campaign->character_status === 'Unconscious' && $campaign->current_hp > 0) {
                    $campaign->character_status = 'Alive';
                }

                $campaign->save();

                // Decrement inventory
                if ($inventory->quantity > 1) {
                    $inventory->quantity -= 1;
                    $inventory->save();
                } else {
                    $inventory->delete();
                }

                return [
                    'success' => true,
                    'message' => "Kamu meminum {$item->name} dan memulihkan {$restored} HP!",
                    'campaign' => $campaign->fresh(),
                    'item' => $item,
                    'hp_restored' => $restored,
                ];
            }

            // Handle equipment toggling
            if (in_array($item->category, ['weapon', 'armor'])) {
                $inventory->is_equipped = !$inventory->is_equipped;
                $inventory->save();

                $statusMsg = $inventory->is_equipped ? 'mengenakan' : 'melepaskan';

                return [
                    'success' => true,
                    'message' => "Kamu telah {$statusMsg} {$item->name}.",
                    'campaign' => $campaign->fresh(),
                    'item' => $item,
                    'is_equipped' => $inventory->is_equipped,
                ];
            }

            return [
                'success' => false,
                'message' => "Item ini ({$item->name}) tidak dapat digunakan secara langsung.",
            ];
        });
    }

    /**
     * Guess FontAwesome icon for category.
     */
    private function guessItemIcon(string $category): string
    {
        return match ($category) {
            'weapon' => 'fa-sword',
            'armor' => 'fa-shield-halved',
            'potion' => 'fa-flask-round-potion',
            'quest' => 'fa-scroll',
            default => 'fa-bag-shopping',
        };
    }
}
