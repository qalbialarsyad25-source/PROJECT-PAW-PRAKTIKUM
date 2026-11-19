<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Filkom D&D') - AI Text RPG</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=MedievalSharp&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --bg-main: #0c0e14;
            --bg-card: #131722;
            --bg-card-hover: #191e2b;
            --bg-panel: #161a26;
            --border-subtle: #242938;
            --border-gold: #c59b27;
            --border-gold-glow: rgba(197, 155, 39, 0.35);
            --gold-primary: #d4af37;
            --gold-bright: #f59e0b;
            --gold-hover: #fbbf24;
            --amber-glow: rgba(245, 158, 11, 0.25);
            --crimson: #dc2626;
            --crimson-glow: rgba(220, 38, 38, 0.3);
            --emerald: #10b981;
            --sapphire: #3b82f6;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --font-display: 'Cinzel', serif;
            --font-fantasy: 'MedievalSharp', cursive;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(ellipse at 50% 0%, rgba(212, 175, 55, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 10% 90%, rgba(19, 23, 34, 0.8) 0%, transparent 50%),
                linear-gradient(180deg, #090a0f 0%, #0c0e14 100%);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        /* Typography utility */
        .font-display { font-family: var(--font-display); }
        .font-fantasy { font-family: var(--font-fantasy); }
        .font-body { font-family: var(--font-body); }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f121b;
        }
        ::-webkit-scrollbar-thumb {
            background: #282f42;
            border-radius: 4px;
            border: 1px solid rgba(197, 155, 39, 0.2);
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-primary);
        }

        /* Medieval Frame Borders & Glows */
        .rpg-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            position: relative;
            transition: all 0.25s ease;
        }
        .rpg-card:hover {
            border-color: rgba(197, 155, 39, 0.4);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4), 0 0 15px var(--amber-glow);
        }

        .gold-border {
            border: 1px solid rgba(212, 175, 55, 0.45);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.15);
        }

        .gold-text-glow {
            color: var(--gold-bright);
            text-shadow: 0 0 12px rgba(245, 158, 11, 0.4);
        }

        /* RPG Buttons */
        .btn-rpg {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.88rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-rpg-gold {
            background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
            color: #0c0e14;
            border: 1px solid #f59e0b;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
        }
        .btn-rpg-gold:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #d4af37 100%);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
            transform: translateY(-2px);
        }
        .btn-rpg-gold:active {
            transform: translateY(0);
        }

        .btn-rpg-dark {
            background: #181d2a;
            color: var(--gold-bright);
            border: 1px solid rgba(212, 175, 55, 0.4);
        }
        .btn-rpg-dark:hover {
            background: #202738;
            border-color: var(--gold-bright);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-rpg-danger {
            background: rgba(220, 38, 38, 0.15);
            color: #f87171;
            border: 1px solid rgba(220, 38, 38, 0.4);
        }
        .btn-rpg-danger:hover {
            background: #dc2626;
            color: #fff;
        }

        /* Navigation Header */
        .navbar {
            background: rgba(12, 14, 20, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }
        .brand-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #2a2212 0%, #151821 100%);
            border: 2px solid var(--gold-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-bright);
            font-size: 1.3rem;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
            transition: transform 0.3s ease;
        }
        .brand-link:hover .brand-icon {
            transform: rotate(15deg) scale(1.05);
        }
        .brand-title {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            color: #fff;
        }
        .brand-subtitle {
            font-size: 0.72rem;
            color: var(--gold-primary);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }
        .nav-item a, .nav-item button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            color: #cbd5e1;
            font-size: 0.92rem;
            font-weight: 500;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            cursor: pointer;
        }
        .nav-item a:hover, .nav-item button:hover {
            color: var(--gold-bright);
            background: rgba(212, 175, 55, 0.1);
        }
        .nav-item a.active {
            color: var(--gold-bright);
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        /* Container & Grid layout */
        .main-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 30px 20px;
            flex: 1;
            width: 100%;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-family: var(--font-display);
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #e2e8f0;
        }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            background: #0f121b;
            border: 1px solid #282f42;
            border-radius: 8px;
            color: #f1f5f9;
            font-family: var(--font-body);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--gold-primary);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.25);
            background: #141824;
        }
        .form-error {
            color: #f87171;
            font-size: 0.82rem;
            margin-top: 6px;
        }

        /* Footer */
        .app-footer {
            background: #080a0e;
            border-top: 1px solid rgba(212, 175, 55, 0.15);
            padding: 24px 20px;
            text-align: center;
            color: #64748b;
            font-size: 0.85rem;
        }

        /* Responsive Mobile menu toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: 1px solid #2d3345;
            color: #cbd5e1;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .menu-toggle { display: block; }
            .nav-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 72px;
                left: 0;
                right: 0;
                background: #0d1017;
                border-bottom: 1px solid rgba(212, 175, 55, 0.25);
                padding: 15px;
                gap: 10px;
            }
            .nav-links.show { display: flex; }
            .nav-item a, .nav-item button { width: 100%; justify-content: flex-start; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="navbar">
        <div class="nav-container">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <div class="brand-icon">
                    <i class="fa-solid fa-dice-d20"></i>
                </div>
                <div>
                    <h1 class="brand-title">FILKOM <span style="color: var(--gold-bright);">D&D</span></h1>
                    <p class="brand-subtitle">Text RPG & AI Dungeon Master</p>
                </div>
            </a>

            <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <nav>
                <ul class="nav-links" id="navLinks">
                    @auth
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-shield-halved"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('characters.index') }}" class="{{ request()->routeIs('characters.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-ninja"></i> Karakter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('campaigns.index') }}" class="{{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-dungeon"></i> Campaign
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-scroll"></i> Profil
                            </a>
                        </li>
                        <li class="nav-item" style="margin-left: 10px;">
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-rpg btn-rpg-danger" style="padding: 6px 14px; font-size: 0.8rem;">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">
                                <i class="fa-solid fa-key"></i> Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('register') }}" class="btn-rpg btn-rpg-gold" style="padding: 8px 16px;">
                                <i class="fa-solid fa-feather-pointed"></i> Daftar Akun
                            </a>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="main-container">
        @include('components.alert')
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="app-footer">
        <p class="font-display" style="letter-spacing: 0.08em; margin-bottom: 6px;">
            FILKOM D&D &bull; PETUALANGAN RPG BERBASIS TEKS &bull; GOOGLE GEMINI AI DUNGEON MASTER
        </p>
        <p style="font-size: 0.8rem; color: #475569;">
            Dirancang dengan arsitektur Laravel yang aman, sistem aturan D&D 5e, serta backend validasi deterministik.
        </p>
    </footer>

    <script>
        // Toggle mobile nav
        const toggleBtn = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        if (toggleBtn && navLinks) {
            toggleBtn.addEventListener('click', () => {
                navLinks.classList.toggle('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
