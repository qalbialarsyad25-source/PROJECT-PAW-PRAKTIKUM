<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gerbang Petualangan') - Filkom D&D</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=MedievalSharp&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --bg-main: #0a0c12;
            --bg-card: #131722;
            --gold-primary: #d4af37;
            --gold-bright: #f59e0b;
            --amber-glow: rgba(245, 158, 11, 0.25);
            --font-display: 'Cinzel', serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--bg-main);
            color: #e2e8f0;
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            background-image: 
                radial-gradient(circle at 50% 30%, rgba(212, 175, 55, 0.12) 0%, transparent 60%),
                linear-gradient(180deg, #07090e 0%, #0d1017 100%);
            background-attachment: fixed;
        }

        .auth-card {
            width: 100%;
            max-width: 480px;
            background: rgba(19, 23, 34, 0.95);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 14px;
            padding: 36px 32px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6), 0 0 25px rgba(212, 175, 55, 0.15);
            position: relative;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-icon {
            display: inline-flex;
            width: 58px;
            height: 58px;
            background: linear-gradient(135deg, #2c2210 0%, #151821 100%);
            border: 2px solid var(--gold-primary);
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--gold-bright);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
            margin-bottom: 12px;
        }

        .auth-title {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.05em;
        }

        .auth-subtitle {
            font-size: 0.88rem;
            color: #94a3b8;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-family: var(--font-display);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #cbd5e1;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            background: #0b0d14;
            border: 1px solid #282f42;
            border-radius: 8px;
            color: #fff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--gold-primary);
            box-shadow: 0 0 12px rgba(212, 175, 55, 0.25);
            background: #111520;
        }

        .form-error {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
            color: #0c0e14;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.35);
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #d4af37 100%);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.88rem;
            color: #94a3b8;
        }

        .auth-footer a {
            color: var(--gold-bright);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-brand">
            <div class="auth-icon">
                <i class="fa-solid fa-dice-d20"></i>
            </div>
            <h1 class="auth-title">FILKOM <span style="color: var(--gold-bright);">D&D</span></h1>
            <p class="auth-subtitle">@yield('subtitle', 'Pintu Gerbang Petualangan AI')</p>
        </div>

        @include('components.alert')

        @yield('content')
    </div>
</body>
</html>
