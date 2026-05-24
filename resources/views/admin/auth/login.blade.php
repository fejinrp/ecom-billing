<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Secure ERP Access – MTL Mart</title>
    <meta name="description" content="MTL Mart Administrative ERP Portal – secure login for authorized personnel only.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand-blue:    #1c3fce;
            --brand-blue-dk: #1330a8;
            --brand-orange:  #f76b1c;
            --brand-orange-dk: #d9560e;
            --bg-panel:      #f0f4ff;
            --bg-form:       #ffffff;
            --text-heading:  #0f1b3d;
            --text-body:     #4a5568;
            --text-muted:    #8a96b0;
            --border:        #dde3f0;
            --input-bg:      #f6f8ff;
            --shadow-card:   0 24px 64px rgba(28,63,206,0.12);
            --shadow-btn:    0 8px 24px rgba(247,107,28,0.35);
            --radius-card:   20px;
            --radius-input:  12px;
            --transition:    all 0.22s ease;
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg-panel);
            color: var(--text-heading);
            overflow: hidden;
        }

        /* ── Animated background mesh ── */
        .bg-mesh {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 50% at 20% -10%, rgba(28,63,206,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 110%, rgba(247,107,28,0.08) 0%, transparent 55%),
                linear-gradient(150deg, #eef2ff 0%, #f5f7ff 50%, #fff8f4 100%);
            z-index: 0;
        }

        /* Floating decorative circles */
        .blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .blob-1 { width: 420px; height: 420px; background: rgba(28,63,206,0.08); top: -120px; left: -100px; animation-delay: 0s; }
        .blob-2 { width: 360px; height: 360px; background: rgba(247,107,28,0.07); bottom: -80px; right: -80px; animation-delay: -4s; }
        .blob-3 { width: 240px; height: 240px; background: rgba(99,102,241,0.06); top: 40%; right: 25%; animation-delay: -8s; }

        @keyframes drift {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(20px, 30px) scale(1.05); }
        }

        /* ── Page wrapper ── */
        .page-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        /* ── Split card ── */
        .login-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px;
            width: 100%;
            border-radius: var(--radius-card);
            background: var(--bg-form);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            border: 1px solid rgba(28,63,206,0.08);
            animation: cardEntrance 0.55s cubic-bezier(0.34,1.56,0.64,1) both;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(28px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── Left Panel (Brand/Visual) ── */
        .brand-panel {
            background: linear-gradient(145deg, var(--brand-blue) 0%, #0d266b 100%);
            padding: 56px 44px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -100px; right: -100px;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: rgba(247,107,28,0.12);
            bottom: -60px; left: -60px;
        }

        /* Logo */
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }
        .logo-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand-orange), #ffb347);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 20px rgba(247,107,28,0.4);
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 20px;
            color: white;
            letter-spacing: -0.5px;
        }
        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 22px;
            color: #ffffff;
            letter-spacing: -0.3px;
        }
        .logo-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            margin-top: 1px;
        }

        /* Brand mid content */
        .brand-body {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 0;
        }
        .brand-headline {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .brand-headline span {
            background: linear-gradient(90deg, #ffb347, var(--brand-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.7;
            max-width: 260px;
        }

        /* Stats row */
        .brand-stats {
            display: flex;
            gap: 28px;
            margin-top: 36px;
            position: relative;
            z-index: 2;
        }
        .stat-item {}
        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
        }
        .stat-label {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        /* Decorative shopping cart SVG */
        .brand-illustration {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }
        .feature-chip {
            display: flex;
            align-items: center;
            gap: 7px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 100px;
            padding: 8px 14px;
            font-size: 12px;
            color: rgba(255,255,255,0.8);
            backdrop-filter: blur(8px);
            font-weight: 500;
            transition: var(--transition);
        }
        .feature-chip:hover {
            background: rgba(255,255,255,0.14);
        }
        .feature-chip i { font-size: 11px; color: var(--brand-orange); }

        /* Brand footer */
        .brand-footer {
            position: relative;
            z-index: 2;
        }
        .brand-footer p {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            line-height: 1.6;
        }

        /* ── Right Panel (Form) ── */
        .form-panel {
            padding: 52px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--bg-form);
        }

        .form-header { margin-bottom: 32px; }
        .form-eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--brand-orange);
            margin-bottom: 8px;
        }
        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.2;
        }
        .form-subtitle {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 6px;
            line-height: 1.6;
        }

        /* Error alert */
        .alert-error {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease;
        }
        .alert-error .alert-icon {
            color: #e53e3e;
            font-size: 15px;
            margin-top: 1px;
            flex-shrink: 0;
        }
        .alert-error .alert-title {
            font-size: 13px;
            font-weight: 700;
            color: #c53030;
            display: block;
            margin-bottom: 3px;
        }
        .alert-error .alert-msg {
            font-size: 12px;
            color: #9b2c2c;
            display: block;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Form group */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-body);
            margin-bottom: 8px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s;
        }
        .form-input {
            width: 100%;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-input);
            padding: 13px 14px 13px 42px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text-heading);
            outline: none;
            transition: var(--transition);
            -webkit-appearance: none;
        }
        .form-input::placeholder { color: #b0bdd4; }
        .form-input:focus {
            border-color: var(--brand-blue);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(28,63,206,0.08);
        }
        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--brand-blue);
        }

        /* Password toggle */
        .pw-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 14px;
            padding: 0;
            transition: color 0.2s;
            display: flex;
            align-items: center;
        }
        .pw-toggle:hover { color: var(--brand-blue); }

        /* Remember me + options row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            margin-top: -4px;
        }
        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-check {
            width: 17px; height: 17px;
            accent-color: var(--brand-blue);
            cursor: pointer;
            border-radius: 4px;
        }
        .remember-label {
            font-size: 13px;
            color: var(--text-body);
            font-weight: 500;
            cursor: pointer;
            user-select: none;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 15px 24px;
            border-radius: var(--radius-input);
            background: linear-gradient(135deg, var(--brand-orange) 0%, #e85d10 100%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-btn);
            transition: all 0.22s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(247,107,28,0.45);
        }
        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active {
            transform: translateY(0) scale(0.985);
            box-shadow: var(--shadow-btn);
        }
        .btn-submit i { font-size: 14px; }

        /* Divider */
        .form-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }
        .form-divider hr {
            flex: 1;
            border: none;
            border-top: 1px solid var(--border);
        }
        .form-divider span {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* Trust badges */
        .trust-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-top: 24px;
        }
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 500;
        }
        .trust-badge i {
            font-size: 12px;
            color: #68d391;
        }

        /* Security bar */
        .security-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 20px;
        }
        .security-bar i { color: #22c55e; font-size: 13px; }
        .security-bar span { font-size: 12px; color: #166534; font-weight: 500; }

        /* ── Responsive ── */
        @media (max-width: 720px) {
            html, body { overflow: auto; }
            .login-card { grid-template-columns: 1fr; }
            .brand-panel { padding: 36px 28px 28px; }
            .brand-headline { font-size: 24px; }
            .brand-stats { gap: 20px; margin-top: 20px; }
            .brand-illustration { flex-wrap: wrap; margin-top: 16px; }
            .form-panel { padding: 36px 28px; }
        }

        @media (max-width: 480px) {
            .form-panel { padding: 28px 20px; }
            .brand-panel { padding: 28px 20px 24px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .blob, .btn-submit, .form-input, .login-card { animation: none; transition: none; }
        }
    </style>
</head>
<body>
    <!-- Animated background -->
    <div class="bg-mesh"></div>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="page-wrapper">
        <div class="login-card">

            <!-- ══ LEFT: Brand Panel ══ -->
            <div class="brand-panel">
                <!-- Logo -->
                <div class="brand-logo">
                    <div class="logo-icon">M</div>
                    <div>
                        <div class="logo-text">MTL Mart</div>
                        <span class="logo-badge">ERP Platform</span>
                    </div>
                </div>

                <!-- Headline -->
                <div class="brand-body">
                    <h1 class="brand-headline">
                        Your Store.<br>
                        <span>Fully In Control.</span>
                    </h1>
                    <p class="brand-sub">
                        Manage products, orders, customers, and reports — all from one powerful admin portal.
                    </p>

                    <!-- Stats -->
                    <div class="brand-stats">
                        <div class="stat-item">
                            <div class="stat-number">360°</div>
                            <div class="stat-label">Dashboard</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">Real-time</div>
                            <div class="stat-label">Analytics</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">Multi-role</div>
                            <div class="stat-label">Access</div>
                        </div>
                    </div>

                    <!-- Feature chips -->
                    <div class="brand-illustration">
                        <div class="feature-chip">
                            <i class="fa-solid fa-box"></i> Orders
                        </div>
                        <div class="feature-chip">
                            <i class="fa-solid fa-chart-line"></i> Reports
                        </div>
                        <div class="feature-chip">
                            <i class="fa-solid fa-users"></i> Agents
                        </div>
                    </div>
                </div>

                <!-- Brand footer -->
                <div class="brand-footer">
                    <p>MTL Mart ERP &copy; {{ date('Y') }}<br>Authorized personnel access only.</p>
                </div>
            </div>

            <!-- ══ RIGHT: Form Panel ══ -->
            <div class="form-panel">
                <div class="form-header">
                    <p class="form-eyebrow">Admin Portal</p>
                    <h2 class="form-title">Welcome Back</h2>
                    <p class="form-subtitle">Sign in to your account to manage the store.</p>
                </div>

                <!-- Error alerts -->
                @if ($errors->any())
                    <div class="alert-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation alert-icon"></i>
                        <div>
                            <span class="alert-title">Authentication Failed</span>
                            @foreach ($errors->all() as $error)
                                <span class="alert-msg">{{ $error }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
                    @csrf

                    <!-- Username / Email -->
                    <div class="form-group">
                        <label class="form-label" for="login_field">Username or Email</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input
                                type="text"
                                name="login_field"
                                id="login_field"
                                required
                                autofocus
                                autocomplete="username"
                                value="{{ old('login_field') }}"
                                placeholder="admin or staff@mtlmart.com"
                                class="form-input"
                                aria-label="Username or Email"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fa-regular fa-lock input-icon"></i>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="form-input"
                                style="padding-right: 44px;"
                                aria-label="Password"
                            >
                            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password visibility">
                                <i class="fa-regular fa-eye" id="pwIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-options">
                        <label class="remember-wrap" for="remember">
                            <input type="checkbox" name="remember" id="remember" class="remember-check">
                            <span class="remember-label">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span id="btnText">Sign In to Dashboard</span>
                        <i class="fa-solid fa-arrow-right-long" id="btnIcon"></i>
                    </button>
                </form>

                <!-- Trust indicators -->
                <div class="form-divider">
                    <hr><span>Secure Connection</span><hr>
                </div>

                <div class="security-bar">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>256-bit SSL encrypted • Session-protected access</span>
                </div>

                <div class="trust-row">
                    <span class="trust-badge"><i class="fa-solid fa-circle-check"></i> Verified Platform</span>
                    <span class="trust-badge"><i class="fa-solid fa-circle-check"></i> GDPR Compliant</span>
                    <span class="trust-badge"><i class="fa-solid fa-circle-check"></i> Audit Logged</span>
                </div>
            </div>

        </div><!-- .login-card -->
    </div><!-- .page-wrapper -->

    <script>
        // Password visibility toggle
        const pwToggle = document.getElementById('pwToggle');
        const pwInput  = document.getElementById('password');
        const pwIcon   = document.getElementById('pwIcon');

        pwToggle.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            pwIcon.className = isHidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });

        // Button loading state on submit
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText   = document.getElementById('btnText');
        const btnIcon   = document.getElementById('btnIcon');

        loginForm.addEventListener('submit', () => {
            submitBtn.disabled = true;
            btnText.textContent = 'Authenticating…';
            btnIcon.className   = 'fa-solid fa-spinner fa-spin';
            submitBtn.style.opacity = '0.85';
        });

        // Focus ring auto-apply for input icon color
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', () => {
                const icon = input.closest('.input-wrapper')?.querySelector('.input-icon');
                if (icon) icon.style.color = 'var(--brand-blue)';
            });
            input.addEventListener('blur', () => {
                const icon = input.closest('.input-wrapper')?.querySelector('.input-icon');
                if (icon) icon.style.color = '';
            });
        });
    </script>
</body>
</html>
