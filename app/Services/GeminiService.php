<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('gemini.api_key', '');
        $this->model = (string) config('gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = (string) config('gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');
        $this->timeout = (int) config('gemini.timeout', 30);
    }

    /**
     * Send prompt context to Gemini API and receive structured JSON response.
     *
     * @param array $context Detailed game and character state prepared by StoryService
     * @return array Validated D&D game outcome structured array
     */
    public function generateTurnOutcome(array $context): array
    {
        // If API key is empty, invoke the smart procedural fallback
        if (empty(trim($this->apiKey))) {
            Log::info('GeminiService: GEMINI_API_KEY is not configured. Running intelligent procedural D&D simulation.');
            return $this->generateProceduralSimulation($context);
        }

        $systemInstruction = $this->buildSystemInstruction();
        $userPrompt = $this->formatUserPrompt($context);

        $endpoint = "{$this->baseUrl}/models/{$this->model}:generateContent";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemInstruction . "\n\n" . $userPrompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => (float) config('gemini.temperature', 0.85),
                'maxOutputTokens' => (int) config('gemini.max_output_tokens', 1500),
            ],
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])
                ->retry(2, 500)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!empty($rawText)) {
                    $parsed = $this->parseAndValidateJson($rawText);
                    if ($parsed !== null) {
                        return $parsed;
                    }
                }

                Log::warning('GeminiService: Empty or malformed response received from Gemini API.', [
                    'status' => $response->status(),
                ]);
            } else {
                Log::error('GeminiService: HTTP request to Gemini API failed.', [
                    'status' => $response->status(),
                    'error' => $response->json('error.message') ?? 'Unknown error',
                ]);
            }
        } catch (Exception $e) {
            Log::error('GeminiService: Exception occurred during API request.', [
                'message' => $e->getMessage(),
            ]);
        }

        // Graceful fallback to procedural simulation if network or API failed
        Log::info('GeminiService: Falling back to procedural D&D simulation engine.');
        return $this->generateProceduralSimulation($context);
    }

    /**
     * Clean and validate JSON string from AI response.
     */
    protected function parseAndValidateJson(string $rawText): ?array
    {
        $cleanText = trim($rawText);

        // Strip markdown code fence if present
        if (str_starts_with($cleanText, '```json')) {
            $cleanText = substr($cleanText, 7);
        } elseif (str_starts_with($cleanText, '```')) {
            $cleanText = substr($cleanText, 3);
        }

        if (str_ends_with($cleanText, '```')) {
            $cleanText = substr($cleanText, 0, -3);
        }

        $cleanText = trim($cleanText);

        $decoded = json_decode($cleanText, true);

        if (!is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('GeminiService: Failed to parse JSON string: ' . substr($cleanText, 0, 100));
            return null;
        }

        // Validate essential keys
        if (!isset($decoded['narrative']) || empty(trim($decoded['narrative']))) {
            return null;
        }

        // Ensure safe normalized structure
        return [
            'narrative' => (string) $decoded['narrative'],
            'action_result' => in_array($decoded['action_result'] ?? '', ['success', 'failure', 'critical_success', 'critical_failure', 'partial_success'])
                ? $decoded['action_result']
                : 'success',
            'reasoning' => (string) ($decoded['reasoning'] ?? 'Tindakan diproses berdasarkan kondisi karakter.'),
            'hp_change' => (int) ($decoded['hp_change'] ?? 0),
            'xp_change' => max(0, (int) ($decoded['xp_change'] ?? 0)),
            'level_up' => (bool) ($decoded['level_up'] ?? false),
            'inventory_changes' => is_array($decoded['inventory_changes'] ?? null) ? $decoded['inventory_changes'] : [],
            'enemy_or_npc' => is_array($decoded['enemy_or_npc'] ?? null) ? $decoded['enemy_or_npc'] : null,
            'campaign_status' => ($decoded['campaign_status'] ?? 'Active') === 'Completed' ? 'Completed' : 'Active',
        ];
    }

    /**
     * System prompt instructions for Gemini as D&D Dungeon Master.
     */
    protected function buildSystemInstruction(): string
    {
        return <<<'INSTRUCTION'
Kamu adalah seorang Dungeon Master (DM) Dungeons & Dragons (D&D) profesional, kreatif, dan imersif.
Bahasa yang kamu gunakan adalah Bahasa Indonesia yang bernuansa sastra abad pertengahan / dark fantasy yang elegan, mendebarkan, dan epik.

Tugasmu:
1. Menganalisis tindakan pemain secara realistis berdasarkan aturan D&D 5e:
   - Pertimbangkan Ras, Kelas, Level, HP saat ini, dan 6 Statistik Karakter (STR, DEX, CON, INT, WIS, CHA) beserta modifikatornya.
   - Pertimbangkan inventaris dan peralatan yang sedang dipakai.
   - Pertimbangkan tingkat kesulitan campaign (Easy, Medium, Hard, Legendary) dan situasi terkini.
2. Simulasikan lemparan dadu D20 secara adil di balik layar (D20 + modifikator stat vs Difficulty Class / Armor Class):
   - Critical Success (Natural 20): Hasil spektakuler, keuntungan tak terduga atau drop item istimewa.
   - Success: Tindakan berhasil dilakukan sesuai harapan.
   - Partial Success: Berhasil namun ada pengorbanan kecil, sedikit luka, atau komplikasi.
   - Failure: Gagal mencapai target, kemungkinan menerima serangan balik atau kehilangan posisi.
   - Critical Failure (Natural 1): Kegagalan fatal atau blunder fatal dengan konsekuensi nyata.
3. Menghasilkan narasi cerita dinamis yang melanjutkan petualangan, memperkenalkan NPC, monster, misteri, teka-teki, atau ruangan dungeon baru.
4. Kamu HARUS mengembalikan respons dalam format JSON valid yang strictly mematuhi struktur berikut tanpa teks markdown di luar JSON:

{
  "narrative": "Teks narasi cerita mendalam dari DM dalam Bahasa Indonesia (2-4 paragraf menarik yang merespons tindakan pemain dan menggambarkan perkembangan suasana)",
  "action_result": "success | failure | critical_success | critical_failure | partial_success",
  "reasoning": "Penjelasan singkat mekanisme D&D (misal: Cek Kekuatan DC 13, roll d20+3 = 16, berhasil membuka gerbang batu kuno)",
  "hp_change": 0, // Nilai negatif untuk kerusakan (misal: -5), positif untuk pemulihan (misal: 10), atau 0 jika tidak ada luka
  "xp_change": 50, // XP yang diperoleh dari keberhasilan, eksplorasi, atau pertarungan (integer positif, biasanya 20-150)
  "level_up": false, // Boolean jika dirasa pemain layak naik tingkat
  "inventory_changes": [
    // Jika mendapatkan item baru atau kehilangan item. Kosongkan [] jika tidak ada.
    {
      "action": "add", // "add" atau "remove"
      "name": "Nama Item",
      "description": "Deskripsi item",
      "category": "weapon | armor | potion | equipment | quest",
      "quantity": 1,
      "effect_type": "heal | damage | buff_ac",
      "effect_value": 15
    }
  ],
  "enemy_or_npc": {
    "name": "Nama NPC atau Monster",
    "type": "npc | enemy",
    "hp": 12,
    "status": "hostile | friendly | neutral"
  }, // atau null jika tidak ada monster/NPC penting baru
  "campaign_status": "Active" // "Active" atau "Completed" jika petualangan selesai/tujuan akhir tercapai
}
INSTRUCTION;
    }

    /**
     * Format context into user prompt for Gemini.
     */
    protected function formatUserPrompt(array $context): string
    {
        return json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Intelligent procedural fallback engine simulating a D&D Dungeon Master.
     * Used when GEMINI_API_KEY is not set or network is offline.
     */
    protected function generateProceduralSimulation(array $context): array
    {
        $character = $context['character'] ?? [];
        $action = trim($context['player_action'] ?? '');
        $campaign = $context['campaign'] ?? [];
        $difficulty = $campaign['difficulty'] ?? 'Medium';

        $actionLower = strtolower($action);

        // Determine relevant stat based on player action keywords
        $targetStat = 'strength';
        if (preg_match('/(serang|hantam|pukul|tebas|tusuk|dobrak|dorong|angkat|pedang|gada)/i', $actionLower)) {
            $targetStat = 'strength';
        } elseif (preg_match('/(panah|tembak|lari|menghindar|lompat|mengendap|curi|buka gembok|lockpick|belati)/i', $actionLower)) {
            $targetStat = 'dexterity';
        } elseif (preg_match('/(tahan|racun|bertahan|napas|dingin|panas)/i', $actionLower)) {
            $targetStat = 'constitution';
        } elseif (preg_match('/(sihir|mantra|analisis|baca|teliti|investigasi|ilmu|rahasia|arcane)/i', $actionLower)) {
            $targetStat = 'intelligence';
        } elseif (preg_match('/(periksa|lihat|amati|dengar|waspada|merasakan|doa|sembuh|cleric)/i', $actionLower)) {
            $targetStat = 'wisdom';
        } elseif (preg_match('/(bicara|bujuk|ancam|rayu|tipu|diplomasi|sapa|negosiasi)/i', $actionLower)) {
            $targetStat = 'charisma';
        }

        // Extract modifier
        $statString = $character['stats'][$targetStat] ?? '10 (+0)';
        preg_match('/\(([+-]?\d+)\)/', $statString, $matches);
        $mod = isset($matches[1]) ? (int) $matches[1] : 0;

        // Roll virtual D20
        $d20 = mt_rand(1, 20);
        $totalCheck = $d20 + $mod;

        // Set DC based on difficulty
        $dc = match ($difficulty) {
            'Easy' => 10,
            'Medium' => 13,
            'Hard' => 16,
            'Legendary' => 19,
            default => 13,
        };

        $charName = $character['name'] ?? 'Petualang';
        $charClass = $character['class'] ?? 'Warrior';
        $charRace = $character['race'] ?? 'Human';

        $actionResult = 'success';
        $hpChange = 0;
        $xpChange = 25;
        $narrative = '';
        $reasoning = '';
        $inventoryChanges = [];
        $enemyOrNpc = null;
        $campaignStatus = 'Active';

        if ($d20 === 20 || $totalCheck >= $dc + 5) {
            $actionResult = 'critical_success';
            $xpChange = 75;
            $reasoning = "Critical Success! Roll D20: {$d20} + mod {$mod} = {$totalCheck} (vs DC {$dc}). Tindakan dieksekusi dengan kecemerlangan mutlak!";

            if (preg_match('/(serang|hantam|panah|sihir|tebas)/i', $actionLower)) {
                $narrative = "Dengan presisi legendaris, {$charName} melancarkan serangan sempurna. Bilah senjatamu membelah angin dengan kecepatan kilat, mengenai titik vital musuh tanpa celah pertahanan! Musuh tersungkur roboh di atas lantai batu yang dingin, meninggalkan jejak abu mistis yang berkilauan. Kamu menemukan kantung sutra berisi ramuan berharga di reruntuhan.";
                $inventoryChanges[] = [
                    'action' => 'add',
                    'name' => 'Elixir Kristal Bintang',
                    'description' => 'Ramuan langka berkilau yang memulihkan 25 HP dan menambah stamina.',
                    'category' => 'potion',
                    'quantity' => 1,
                    'effect_type' => 'heal',
                    'effect_value' => 25,
                ];
            } else {
                $narrative = "Keberuntungan dewa menaungi langkahmu. Dengan keahlian istimewa sebagai seorang {$charClass}, kamu berhasil '{$action}' melampaui segala ekspektasi. Kamu menemukan sebuah kompartemen rahasia bertatahkan lambang kuno yang berisi jimat keberuntungan!";
                $inventoryChanges[] = [
                    'action' => 'add',
                    'name' => 'Jimat Perlindungan Kuno',
                    'description' => 'Amulet perunggu bercahaya redup yang melindungi pemiliknya dari mara bahaya.',
                    'category' => 'equipment',
                    'quantity' => 1,
                ];
            }
        } elseif ($totalCheck >= $dc) {
            $actionResult = 'success';
            $xpChange = 40;
            $reasoning = "Berhasil! Roll D20: {$d20} + mod {$mod} = {$totalCheck} (vs DC {$dc}). Karakter berhasil mengatasi tantangan.";

            if (preg_match('/(serang|lawan|pukul|hantam)/i', $actionLower)) {
                $narrative = "Seranganmu mendarat telak! Suara benturan senjata bergema di lorong batu. Musuh terhuyung ke belakang seraya mengeluarkan geraman kesakitan. Walau mereka masih bernyawa, inisiatif pertempuran kini berada sepenuhnya dalam genggamanmu.";
                $enemyOrNpc = [
                    'name' => 'Goblin Raider',
                    'type' => 'enemy',
                    'hp' => 6,
                    'status' => 'wounded',
                ];
            } elseif (preg_match('/(bicara|tanya|sapa|bujuk)/i', $actionLower)) {
                $narrative = "Kata-katamu tersusun rapi dengan intonasi yang meyakinkan. Sosok di hadapanmu menurunkan senjatanya sejenak, menatapmu dengan pandangan penuh pertimbangan sebelum mulai membuka rahasia tentang apa yang menantimu di kedalaman.";
                $enemyOrNpc = [
                    'name' => 'Pengembara Misterius',
                    'type' => 'npc',
                    'hp' => 20,
                    'status' => 'friendly',
                ];
            } else {
                $narrative = "Kamu melangkah maju dengan hati-hati. Upayamu '{$action}' berhasil dieksekusi dengan baik. Medan di sekitarmu mulai terbuka, memperlihatkan lorong berbatu yang dipenuhi ukiran kuno di dinding gua.";
            }
        } elseif ($totalCheck >= $dc - 3) {
            $actionResult = 'partial_success';
            $hpChange = -3;
            $xpChange = 20;
            $reasoning = "Partial Success! Roll D20: {$d20} + mod {$mod} = {$totalCheck} (vs DC {$dc}). Berhasil dengan komplikasi atau luka goresan ringan.";
            $narrative = "Tindakanmu berhasil, tetapi tidak tanpa harga. Saat kamu '{$action}', sebuah jebakan tersembunyi terpicu atau musuh sempat membalas dengan cakaran cepat! Kamu berhasil mencapai tujuan, namun menderita luka gores (-3 HP).";
        } elseif ($d20 === 1) {
            $actionResult = 'critical_failure';
            $hpChange = -8;
            $xpChange = 5;
            $reasoning = "Critical Failure! Natural 1 pada roll D20. Blunder fatal tak terhindarkan!";
            $narrative = "Bencana terjadi! Kaki {$charName} terpeleset di atas lumut basah saat berusaha melakukan tindakan. Kehilangan keseimbangan secara tiba-tiba membuatmu terhempas menghantam dinding batu tajam (-8 HP). Suara benturan keras membangunkan entitas kegelapan yang sedang tertidur di sudut ruangan!";
        } else {
            $actionResult = 'failure';
            $hpChange = -4;
            $xpChange = 10;
            $reasoning = "Gagal! Roll D20: {$d20} + mod {$mod} = {$totalCheck} (vs DC {$dc}). Upaya tidak cukup kuat untuk menembus pertahanan rintangan.";
            $narrative = "Usahamu '{$action}' tidak membuahkan hasil. Pertahanan musuh terlalu kokoh atau rintangan di hadapanmu lebih rumit dari dugaan. Serangan balasan menghantam tubuhmu (-4 HP). Kamu harus menyusun kembali strategimu sebelum situasi kian memburuk!";
        }

        return [
            'narrative' => $narrative,
            'action_result' => $actionResult,
            'reasoning' => $reasoning,
            'hp_change' => $hpChange,
            'xp_change' => $xpChange,
            'level_up' => false,
            'inventory_changes' => $inventoryChanges,
            'enemy_or_npc' => $enemyOrNpc,
            'campaign_status' => $campaignStatus,
        ];
    }
}
