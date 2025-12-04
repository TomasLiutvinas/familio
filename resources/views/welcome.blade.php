<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Familio - Family Subscription Management</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon-familio.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,900" rel="stylesheet" />

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                background: #080808;
                color: #e8e8e8;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                position: relative;
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                cursor: default;
            }

            /* Darker animated background */
            body::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background:
                    radial-gradient(circle at 30% 40%, rgba(100, 0, 0, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 70% 60%, rgba(80, 20, 20, 0.1) 0%, transparent 50%);
                animation: drift 25s ease-in-out infinite alternate;
            }

            @keyframes drift {
                0% { transform: translate(0, 0) rotate(0deg); }
                100% { transform: translate(50px, 50px) rotate(5deg); }
            }

            /* Enhanced grain texture */
            body::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.035'/%3E%3C/svg%3E");
                pointer-events: none;
                opacity: 0.6;
            }

            /* Floating subscription cards */
            .floating-card {
                position: absolute;
                border-radius: 12px;
                background: linear-gradient(135deg, rgba(139, 0, 0, 0.08) 0%, rgba(80, 20, 20, 0.05) 100%);
                border: 1px solid rgba(139, 0, 0, 0.15);
                pointer-events: none;
                backdrop-filter: blur(5px);
            }

            .card-1 {
                width: 180px;
                height: 110px;
                top: 15%;
                left: 8%;
                animation: float-card 18s ease-in-out infinite;
            }

            .card-2 {
                width: 160px;
                height: 100px;
                top: 25%;
                right: 10%;
                animation: float-card 22s ease-in-out infinite 2s;
            }

            .card-3 {
                width: 200px;
                height: 120px;
                bottom: 20%;
                left: 12%;
                animation: float-card 20s ease-in-out infinite 4s;
            }

            .card-4 {
                width: 170px;
                height: 105px;
                bottom: 15%;
                right: 8%;
                animation: float-card 25s ease-in-out infinite 1s;
            }

            .card-5 {
                width: 140px;
                height: 90px;
                top: 60%;
                left: 5%;
                animation: float-card 19s ease-in-out infinite 3s;
            }

            .card-6 {
                width: 150px;
                height: 95px;
                top: 50%;
                right: 6%;
                animation: float-card 21s ease-in-out infinite 5s;
            }

            .card-7 {
                width: 130px;
                height: 85px;
                top: 40%;
                left: 15%;
                animation: float-card 23s ease-in-out infinite 6s;
            }

            .card-8 {
                width: 145px;
                height: 92px;
                bottom: 35%;
                right: 12%;
                animation: float-card 24s ease-in-out infinite 7s;
            }

            @keyframes float-card {
                0%, 100% {
                    transform: translate(0, 0) rotate(0deg);
                    opacity: 0.15;
                }
                25% {
                    transform: translate(20px, -20px) rotate(2deg);
                    opacity: 0.25;
                }
                50% {
                    transform: translate(-15px, -40px) rotate(-1deg);
                    opacity: 0.2;
                }
                75% {
                    transform: translate(-25px, -15px) rotate(1deg);
                    opacity: 0.3;
                }
            }

            /* Connection lines between cards */
            .connection-line {
                position: absolute;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(139, 0, 0, 0.2), transparent);
                pointer-events: none;
                transform-origin: left center;
            }

            .line-connect-1 {
                width: 200px;
                top: 20%;
                left: 15%;
                transform: rotate(15deg);
                animation: pulse-line 4s ease-in-out infinite;
            }

            .line-connect-2 {
                width: 180px;
                top: 30%;
                right: 20%;
                transform: rotate(-20deg);
                animation: pulse-line 5s ease-in-out infinite 1s;
            }

            .line-connect-3 {
                width: 220px;
                bottom: 25%;
                left: 18%;
                transform: rotate(-12deg);
                animation: pulse-line 6s ease-in-out infinite 2s;
            }

            .line-connect-4 {
                width: 190px;
                bottom: 20%;
                right: 15%;
                transform: rotate(18deg);
                animation: pulse-line 5.5s ease-in-out infinite 1.5s;
            }

            @keyframes pulse-line {
                0%, 100% { opacity: 0.1; }
                50% { opacity: 0.3; }
            }

            /* Subtle grid pattern */
            .grid-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image:
                    linear-gradient(rgba(139, 0, 0, 0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(139, 0, 0, 0.03) 1px, transparent 1px);
                background-size: 80px 80px;
                pointer-events: none;
                opacity: 0.3;
            }

            /* Glowing nodes at intersection points */
            .node {
                position: absolute;
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: rgba(185, 28, 28, 0.4);
                box-shadow: 0 0 15px rgba(185, 28, 28, 0.3);
                pointer-events: none;
            }

            .node-1 {
                top: 22%;
                left: 18%;
                animation: node-pulse 3s ease-in-out infinite;
            }

            .node-2 {
                top: 28%;
                right: 22%;
                animation: node-pulse 3.5s ease-in-out infinite 0.5s;
            }

            .node-3 {
                bottom: 27%;
                left: 20%;
                animation: node-pulse 4s ease-in-out infinite 1s;
            }

            .node-4 {
                bottom: 22%;
                right: 18%;
                animation: node-pulse 3.8s ease-in-out infinite 1.5s;
            }

            .node-5 {
                top: 45%;
                left: 25%;
                animation: node-pulse 4.2s ease-in-out infinite 2s;
            }

            .node-6 {
                bottom: 40%;
                right: 28%;
                animation: node-pulse 3.6s ease-in-out infinite 2.5s;
            }

            @keyframes node-pulse {
                0%, 100% {
                    transform: scale(1);
                    opacity: 0.4;
                }
                50% {
                    transform: scale(1.5);
                    opacity: 0.8;
                }
            }

            @keyframes container-glow {
                0%, 100% {
                    box-shadow:
                        0 30px 80px rgba(0, 0, 0, 0.7),
                        0 0 80px rgba(127, 29, 29, 0.15),
                        inset 0 1px 1px rgba(255, 255, 255, 0.04);
                }
                50% {
                    box-shadow:
                        0 30px 80px rgba(0, 0, 0, 0.7),
                        0 0 120px rgba(127, 29, 29, 0.25),
                        inset 0 1px 1px rgba(255, 255, 255, 0.04);
                }
            }

            .container {
                text-align: center;
                padding: 3rem 2.5rem;
                max-width: 700px;
                position: relative;
                z-index: 10;
                background: linear-gradient(135deg, rgba(18, 18, 18, 0.85) 0%, rgba(25, 15, 15, 0.75) 100%);
                backdrop-filter: blur(30px);
                border-radius: 32px;
                border: 1px solid rgba(127, 29, 29, 0.25);
                animation: container-glow 4s ease-in-out infinite;
            }

            .logo-section {
                margin-bottom: 2rem;
                position: relative;
            }

            .logo-wrapper {
                display: inline-block;
                position: relative;
                width: 180px;
                height: 180px;
            }

            .logo {
                width: 100%;
                height: 100%;
                position: relative;
                animation: gentle-pulse 4s ease-in-out infinite;
            }

            @keyframes gentle-pulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.03); opacity: 0.95; }
            }

            /* Circular illustration */
            .circle-outer {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 120px;
                height: 120px;
                border-radius: 50%;
                border: 3px solid rgba(185, 28, 28, 0.45);
                box-shadow: 0 0 30px rgba(185, 28, 28, 0.25),
                            inset 0 0 20px rgba(185, 28, 28, 0.1);
            }

            .circle-middle {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 80px;
                height: 80px;
                border-radius: 50%;
                border: 2px solid rgba(220, 38, 38, 0.55);
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
                animation: rotate-slow 20s linear infinite;
            }

            .circle-inner {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(239, 68, 68, 0.7) 0%, rgba(185, 28, 28, 0.4) 100%);
                box-shadow: 0 0 30px rgba(239, 68, 68, 0.5),
                            inset 0 0 15px rgba(239, 68, 68, 0.25);
                animation: pulse-glow 3s ease-in-out infinite;
            }

            /* Connecting lines */
            .line {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 2px;
                height: 60px;
                background: linear-gradient(to bottom, rgba(185, 28, 28, 0.6), transparent);
                transform-origin: top center;
            }

            .line-1 {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            .line-2 {
                transform: translate(-50%, -50%) rotate(90deg);
            }

            .line-3 {
                transform: translate(-50%, -50%) rotate(180deg);
            }

            .line-4 {
                transform: translate(-50%, -50%) rotate(270deg);
            }

            @keyframes rotate-slow {
                0% { transform: translate(-50%, -50%) rotate(0deg); }
                100% { transform: translate(-50%, -50%) rotate(360deg); }
            }

            @keyframes pulse-glow {
                0%, 100% {
                    box-shadow: 0 0 30px rgba(239, 68, 68, 0.5),
                                inset 0 0 15px rgba(239, 68, 68, 0.25);
                }
                50% {
                    box-shadow: 0 0 45px rgba(239, 68, 68, 0.7),
                                inset 0 0 25px rgba(239, 68, 68, 0.35);
                }
            }

            /* Subtle glow rings */
            .glow-ring {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                border-radius: 50%;
                border: 1px solid rgba(139, 0, 0, 0.2);
                pointer-events: none;
            }

            .ring-1 {
                width: 160px;
                height: 160px;
                animation: ring-expand 4s ease-in-out infinite;
            }

            .ring-2 {
                width: 200px;
                height: 200px;
                animation: ring-expand 4s ease-in-out infinite 0.5s;
            }

            @keyframes ring-expand {
                0%, 100% {
                    transform: translate(-50%, -50%) scale(1);
                    opacity: 0.2;
                }
                50% {
                    transform: translate(-50%, -50%) scale(1.15);
                    opacity: 0;
                }
            }

            .brand-name {
                font-size: 4rem;
                font-weight: 900;
                margin-bottom: 1.5rem;
                line-height: 1;
                letter-spacing: -2px;
                background: linear-gradient(135deg, #5a0a0a 0%, #7f1d1d 20%, #991b1b 40%, #b91c1c 50%, #991b1b 60%, #7f1d1d 80%, #5a0a0a 100%);
                background-size: 300% 300%;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                filter: drop-shadow(0 0 12px rgba(127, 29, 29, 0.35)) drop-shadow(0 0 25px rgba(127, 29, 29, 0.15));
                animation: gradient-shimmer 8s ease-in-out infinite;
            }

            @keyframes gradient-shimmer {
                0% {
                    background-position: 0% 50%;
                }
                33% {
                    background-position: 100% 50%;
                }
                66% {
                    background-position: 50% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }

            .tagline {
                font-size: 0.9rem;
                opacity: 0.45;
                margin-bottom: 0.75rem;
                color: #a78b8b;
                font-weight: 500;
                letter-spacing: 3px;
                text-transform: uppercase;
            }

            .description {
                font-size: 1.15rem;
                opacity: 0.6;
                margin-bottom: 2.5rem;
                line-height: 1.7;
                color: #c5c5c5;
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }

            .buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                margin: 2rem 0 3rem 0;
                z-index: 100;
                position: relative;
            }

            .btn {
                padding: 0.875rem 2rem;
                border-radius: 12px;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
                font-size: 0.95rem;
                position: relative;
                overflow: hidden;
                letter-spacing: 0.3px;
                z-index: 101;
                backdrop-filter: blur(10px);
            }

            .btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(127, 29, 29, 0.15), transparent);
                transition: left 0.5s;
            }

            .btn:hover::before {
                left: 100%;
            }

            .btn-primary {
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.25) 0%, rgba(80, 20, 20, 0.2) 100%);
                color: #d4a5a5;
                border: 1px solid rgba(127, 29, 29, 0.4);
                box-shadow: 0 4px 12px rgba(127, 29, 29, 0.15);
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.35) 0%, rgba(80, 20, 20, 0.25) 100%);
                border-color: rgba(127, 29, 29, 0.6);
                box-shadow: 0 6px 20px rgba(127, 29, 29, 0.25);
                color: #e5b8b8;
            }

            .btn-secondary {
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.12) 0%, rgba(80, 20, 20, 0.08) 100%);
                color: #b89090;
                border: 1px solid rgba(127, 29, 29, 0.25);
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 12px rgba(127, 29, 29, 0.1);
            }

            .btn-secondary:hover {
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.2) 0%, rgba(80, 20, 20, 0.15) 100%);
                border-color: rgba(127, 29, 29, 0.4);
                transform: translateY(-3px);
                color: #d4a5a5;
                box-shadow: 0 6px 20px rgba(127, 29, 29, 0.2);
            }

            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid rgba(127, 29, 29, 0.2);
            }

            .feature {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1rem;
                padding: 1.5rem;
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.08) 0%, rgba(80, 20, 20, 0.05) 100%);
                border: 1px solid rgba(127, 29, 29, 0.2);
                border-radius: 16px;
                backdrop-filter: blur(10px);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .feature::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(127, 29, 29, 0.1), transparent);
                transition: left 0.6s;
            }

            .feature:hover {
                transform: translateY(-4px);
                border-color: rgba(127, 29, 29, 0.4);
                background: linear-gradient(135deg, rgba(127, 29, 29, 0.12) 0%, rgba(80, 20, 20, 0.08) 100%);
                box-shadow: 0 8px 25px rgba(127, 29, 29, 0.2);
            }

            .feature:hover::before {
                left: 100%;
            }

            .feature-icon {
                font-size: 2.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .feature-title {
                font-size: 1rem;
                font-weight: 600;
                color: #d4a5a5;
                text-align: center;
                letter-spacing: 0.5px;
            }

            .feature-desc {
                font-size: 0.85rem;
                color: #8a7070;
                text-align: center;
                line-height: 1.5;
                opacity: 0.8;
            }

            /* Decorative corners */
            .corner {
                position: absolute;
                width: 80px;
                height: 80px;
                border: 1px solid rgba(139, 0, 0, 0.15);
                pointer-events: none;
            }

            .corner-tl {
                top: -1px;
                left: -1px;
                border-right: none;
                border-bottom: none;
                border-radius: 32px 0 0 0;
            }

            .corner-br {
                bottom: -1px;
                right: -1px;
                border-left: none;
                border-top: none;
                border-radius: 0 0 32px 0;
            }

            @media (max-width: 768px) {
                .brand-name { font-size: 3rem; }
                .description { font-size: 1rem; }
                .logo { font-size: 4rem; }
                .container { padding: 2.5rem 1.5rem; }
                .features {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: center;
                }
                .buttons { flex-direction: column; width: 100%; }
                .btn { width: 100%; }
                .floating-card { display: none; }
                .connection-line { display: none; }
                .node { display: none; }
            }
        </style>
    </head>
    <body>
        <!-- Grid overlay -->
        <div class="grid-overlay"></div>

        <!-- Floating subscription cards -->
        <div class="floating-card card-1"></div>
        <div class="floating-card card-2"></div>
        <div class="floating-card card-3"></div>
        <div class="floating-card card-4"></div>
        <div class="floating-card card-5"></div>
        <div class="floating-card card-6"></div>
        <div class="floating-card card-7"></div>
        <div class="floating-card card-8"></div>

        <!-- Connection lines -->
        <div class="connection-line line-connect-1"></div>
        <div class="connection-line line-connect-2"></div>
        <div class="connection-line line-connect-3"></div>
        <div class="connection-line line-connect-4"></div>

        <!-- Glowing nodes -->
        <div class="node node-1"></div>
        <div class="node node-2"></div>
        <div class="node node-3"></div>
        <div class="node node-4"></div>
        <div class="node node-5"></div>
        <div class="node node-6"></div>

        <div class="container">
            <div class="corner corner-tl"></div>
            <div class="corner corner-br"></div>

            <div class="logo-section">
                <div class="logo-wrapper">
                    <div class="glow-ring ring-1"></div>
                    <div class="glow-ring ring-2"></div>
                    <div class="logo">
                        <div class="line line-1"></div>
                        <div class="line line-2"></div>
                        <div class="line line-3"></div>
                        <div class="line line-4"></div>
                        <div class="circle-outer"></div>
                        <div class="circle-middle"></div>
                        <div class="circle-inner"></div>
                    </div>
                </div>
            </div>

            <div class="tagline">Family Subscription Manager</div>
            <h1 class="brand-name">Familio</h1>
            <p class="description">Effortlessly track subscriptions, split costs, and manage payments for your family.</p>

            <div class="buttons">
                @auth
                    <a href="{{ url('/admin') }}" class="btn btn-primary">Go to Dashboard</a>
                @else
                    <a href="{{ url('/admin/login') }}" class="btn btn-secondary">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                    @endif
                @endauth
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <div class="feature-title">Cost Splitting</div>
                    <div class="feature-desc">Automatically divide subscription costs among family members</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">💳</div>
                    <div class="feature-title">Payment Tracking</div>
                    <div class="feature-desc">Monitor who paid what and when with ease</div>
                </div>
            </div>
        </div>
    </body>
</html>
