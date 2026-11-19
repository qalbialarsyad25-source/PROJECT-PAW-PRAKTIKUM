<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for integrating Google Gemini API as the AI Dungeon
    | Master (DM) for the D&D RPG game.
    |
    */

    'api_key' => env('GEMINI_API_KEY', ''),

    'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),

    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),

    'timeout' => (int) env('GEMINI_TIMEOUT', 30),

    'temperature' => (float) env('GEMINI_TEMPERATURE', 0.85),

    'max_output_tokens' => (int) env('GEMINI_MAX_TOKENS', 1500),

    'simulation_mode_on_empty_key' => true,
];
