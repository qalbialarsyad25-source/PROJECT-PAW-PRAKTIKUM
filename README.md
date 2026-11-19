# FILKOM D&D - Text-Based RPG with Google Gemini AI Dungeon Master

Aplikasi web game **Role-Playing Game (RPG) berbasis teks** yang terinspirasi dari mekanisme **Dungeons & Dragons (D&D) 5th Edition**. Aplikasi ini dirancang agar pengguna dapat memainkan petualangan fantasi epik secara mandiri (solo play) dengan **Google Gemini AI** yang bertindak sebagai **Dungeon Master (DM)** interaktif, cerdas, dan dinamis tanpa memerlukan DM manusia maupun pemain lain.

---

## 🌟 Fitur Utama

1. **Sistem Autentikasi & Keamanan Penuh**:
   - Registrasi pengguna (Nama, Email unik, Password terkonfirmasi).
   - Login & Remember Me dengan session management yang aman dan proteksi CSRF.
   - Manajemen Profil (melihat dan memperbarui nama, email, serta kata sandi).
   - **Isolasi Data Pengguna yang Ketat**: Setiap pengguna hanya dapat mengakses karakter, campaign, inventaris, dan riwayat petualangan miliknya sendiri.

2. **Character Management (CRUD & D&D 5e Mechanics)**:
   - Pemilihan Ras: *Human, Elf, Dwarf, Halfling, Dragonborn, Half-Orc, Tiefling* dengan bonus rasial spesifik.
   - Pemilihan Kelas: *Warrior, Mage, Archer, Rogue, Cleric* dengan karakteristik, dadu nyawa (Hit Die), dan perlengkapan awal otomatis.
   - 6 Statistik Utama D&D: **Strength (STR)**, **Dexterity (DEX)**, **Constitution (CON)**, **Intelligence (INT)**, **Wisdom (WIS)**, dan **Charisma (CHA)**.
   - Perhitungan modifikator stat deterministik (`floor((stat - 10) / 2)`).
   - Sistem Leveling & Pengalaman (XP) dengan batas ambang level dan peningkatan HP serta statistik otomatis saat naik level.
   - Status karakter (Sehat / Pingsan saat HP mencapai 0).

3. **Campaign Management**:
   - Memulai campaign baru dengan memilih karakter yang telah dibuat.
   - Pengaturan tingkat kesulitan D&D: **Easy** (DC 10), **Medium** (DC 13), **Hard** (DC 16), dan **Legendary** (DC 19).
   - Pemisahan konteks cerita dan riwayat petualangan antarsesi secara terisolasi.
   - Otomatisasi status campaign (*Active* menjadi *Completed* saat mencapai klimaks petualangan).
   - Halaman **Kronik Petualangan** untuk membaca kembali seluruh babak cerita dari awal hingga akhir.

4. **AI Dungeon Master (Gemini API Integration)**:
   - Antarmuka **Digital Adventure Book** yang atmosferik dengan tema *dark fantasy* abad pertengahan.
   - Input tindakan pemain bebas dan natural (contoh: *"aku membuka pintu gua yang berlumut"*, *"aku berbicara ramah dengan penjaga"*, *"aku menebaskan pedang baja ke arah goblin"*).
   - Komunikasi asinkron via **JavaScript Fetch API / AJAX** tanpa reload halaman.
   - **Output Terstruktur JSON**: Gemini AI mengembalikan payload JSON valid yang memuat narasi cerita, hasil evaluasi tindakan (`critical_success`, `success`, `partial_success`, `failure`, `critical_failure`), alasan mekanik D20 + modifikator vs DC, perubahan HP, perolehan XP, perubahan inventaris, dan data monster/NPC.
   - **Backend Sebagai Sumber Kebenaran Utama (Single Source of Truth)**: Nilai perubahan HP dan XP divalidasi dan dibatasi secara ketat oleh backend Laravel dalam Database Transaction.
   - **Simulasi Prosedural Cerdas (Fail-safe Fallback)**: Jika `GEMINI_API_KEY` belum dikonfigurasi atau terjadi gangguan jaringan, sistem otomatis menjalankan simulasi D&D cerdas sehingga game tetap 100% dapat dimainkan.

5. **Sistem Inventaris Interaktif**:
   - Kategori item: Senjata, Zirah/Pelindung, Ramuan (Potions), Alat Penjelajahan, dan Benda Misi (*Quest Item*).
   - Tombol interaktif untuk meminum ramuan penyembuh (HP restore) atau memakai/melepas senjata & zirah secara langsung dari panel petualangan tanpa reload halaman.

---

## 🛠️ Arsitektur & Teknologi

- **Framework Backend**: Laravel 13.x (PHP 8.3)
- **Database**: MySQL 8.x
- **Template Engine**: Laravel Blade (Reusable Components)
- **Frontend**: HTML5, CSS3 (*Dark Fantasy Theme*), JavaScript (Fetch API Vanilla)
- **Font & Icon**: Google Fonts (*Cinzel*, *MedievalSharp*, *Plus Jakarta Sans*), FontAwesome 6
- **AI Engine**: Google Gemini API (`gemini-1.5-flash` / `gemini-2.0-flash`) via Laravel HTTP Client

---

## 📋 Prasyarat Sistem

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terpasang:
- **PHP** versi 8.3 atau lebih baru (dengan ekstensi `pdo_mysql`, `curl`, `mbstring`, `openssl`).
- **Composer** (Dependency Manager PHP).
- **MySQL Database Server** (dapat menggunakan bawaan Laragon, XAMPP, Docker, atau MySQL Server mandiri).

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Kloning atau Buka Direktori Project
Pastikan Anda berada di direktori root aplikasi:
```bash
cd FilkomDND_PAW
```

### 2. Instalasi Dependensi Composer
Jalankan perintah berikut untuk mengunduh semua library PHP yang dibutuhkan:
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh `.env.example` ke `.env` (jika belum ada):
```bash
cp .env.example .env
```
Lalu generate encryption key Laravel:
```bash
php artisan key:generate
```

### 4. Konfigurasi Database MySQL
Buka file `.env` menggunakan teks editor dan sesuaikan kredensial MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=filkom_dnd
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan**: Buat database baru bernama `filkom_dnd` di MySQL Anda (misal via phpMyAdmin, HeidiSQL, atau terminal MySQL):
> ```sql
> CREATE DATABASE IF NOT EXISTS filkom_dnd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
> ```

### 5. Pengaturan Google Gemini API Key
Dapatkan API Key Google Gemini secara gratis di [Google AI Studio](https://aistudio.google.com/app/apikey).
Setelah mendapatkan API Key, tambahkan pada file `.env`:
```env
GEMINI_API_KEY=AIzaSy...your_gemini_api_key_here...
GEMINI_MODEL=gemini-1.5-flash
GEMINI_TIMEOUT=30
```
> **Catatan Pengujian Mandiri**: Jika Anda belum memasukkan `GEMINI_API_KEY`, aplikasi **tetap dapat dimainkan sepenuhnya** karena `GeminiService` memiliki sistem fallback simulasi D&D prosedural otomatis yang mengevaluasi D20 dice roll, ras, kelas, dan modifikator statistik karakter Anda!

### 6. Menjalankan Database Migration & Seeder
Jalankan migrasi untuk membuat seluruh tabel dan seeder data awal (starter items dan akun demo):
```bash
php artisan migrate --seed
```

### 7. Menjalankan Server Aplikasi
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Buka browser dan akses: **`http://127.0.0.1:8000`**

---

## 🛡️ Akun Demo Siap Pakai

Aplikasi telah dilengkapi dengan akun petualang bawaan dari seeder:
- **Email**: `player@dnd.test`
- **Password**: `password123`

Akun demo ini telah memiliki 3 karakter dengan kelas berbeda (*Valerius Ironbrow* - Dwarf Warrior, *Aeloria Moonwhisper* - Elf Mage, dan *Shadowthorn Pip* - Halfling Rogue) serta satu campaign aktif yang siap dimainkan!

---

## 🧪 Menjalankan Pengujian Otomatis (Automated Tests)

Project ini dilengkapi dengan test suite lengkap (23 test kasus & 83 assertions) yang menguji alur autentikasi, isolasi data, mekanisme D&D, dan respon API:
```bash
php artisan test
```

---

## 📁 Struktur Direktori Utama

```
FilkomDND_PAW/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdventureController.php   # Endpoint AJAX aksi pemain & penggunaan item
│   │   │   ├── AuthController.php        # Register, Login, Logout
│   │   │   ├── CampaignController.php    # CRUD Campaign & view adventure
│   │   │   ├── CharacterController.php   # CRUD Karakter
│   │   │   ├── DashboardController.php   # Tampilan beranda & metrik petualang
│   │   │   └── ProfileController.php     # Pengelolaan profil & password
│   │   └── Requests/                     # Validasi form request yang aman
│   ├── Models/
│   │   ├── Campaign.php
│   │   ├── CampaignQuest.php
│   │   ├── Character.php
│   │   ├── Inventory.php
│   │   ├── Item.php
│   │   ├── StoryHistory.php
│   │   └── User.php
│   └── Services/
│       ├── GameService.php               # Aturan permainan D&D 5e, stat modifiers, kalkulasi level & HP
│       ├── GeminiService.php             # Komunikasi Google Gemini API & fail-safe simulation engine
│       └── StoryService.php              # Pengelolaan konteks percakapan & prolog cerita
├── config/
│   └── gemini.php                        # Konfigurasi model, timeout, dan base URL Gemini
├── database/
│   ├── migrations/                       # Skema tabel database MySQL
│   └── seeders/                          # Data item awal & user demo
└── resources/
    └── views/
        ├── auth/                         # Login & Register
        ├── campaigns/                    # Index, Create, Show (Kronik), Adventure (AI DM)
        ├── characters/                   # Index, Create, Show (Lembar Karakter), Edit
        ├── components/                   # Alert, Stat Badge, Character Card
        ├── dashboard/                    # Dashboard aktivitas
        ├── layouts/                      # Master App & Guest Dark Fantasy layout
        └── profile/                      # Form edit profil
```

---

*Selamat berpetualang dan semoga dadu D20 selalu memihak langkahmu di dunia Filkom D&D!*
