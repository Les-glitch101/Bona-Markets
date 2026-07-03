<?php
// ============================================================
// BONA MARKETS – Loading / Welcome Page
// ============================================================
// 
// This is the entry point of the entire site.
// When someone visits your domain, this page loads first,
// shows a welcome animation, then redirects to the main site.
// ============================================================

// Redirect to main site after 3 seconds
// The redirect happens via JavaScript for smooth animation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome to Bona Markets</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@300;400;600&display=swap" rel="stylesheet" />

    <style>
        /* ─── RESET ─────────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #1a1208;
            overflow: hidden;
        }

        /* ─── BACKGROUND ANIMATION ────────────────────────────── */
        .bg-particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-particles span {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(232, 149, 42, 0.15);
            border-radius: 50%;
            animation: floatUp 8s infinite linear;
        }

        .bg-particles span:nth-child(1) { left: 10%; animation-duration: 6s; animation-delay: 0s; width: 8px; height: 8px; }
        .bg-particles span:nth-child(2) { left: 25%; animation-duration: 8s; animation-delay: 2s; width: 4px; height: 4px; }
        .bg-particles span:nth-child(3) { left: 40%; animation-duration: 7s; animation-delay: 1s; width: 10px; height: 10px; }
        .bg-particles span:nth-child(4) { left: 55%; animation-duration: 9s; animation-delay: 3s; width: 5px; height: 5px; }
        .bg-particles span:nth-child(5) { left: 70%; animation-duration: 6.5s; animation-delay: 0.5s; width: 7px; height: 7px; }
        .bg-particles span:nth-child(6) { left: 85%; animation-duration: 8.5s; animation-delay: 2.5s; width: 9px; height: 9px; }
        .bg-particles span:nth-child(7) { left: 50%; animation-duration: 7.5s; animation-delay: 1.5s; width: 6px; height: 6px; }
        .bg-particles span:nth-child(8) { left: 15%; animation-duration: 9.5s; animation-delay: 0.8s; width: 11px; height: 11px; }
        .bg-particles span:nth-child(9) { left: 65%; animation-duration: 6.8s; animation-delay: 3.2s; width: 4px; height: 4px; }
        .bg-particles span:nth-child(10) { left: 90%; animation-duration: 7.8s; animation-delay: 1.2s; width: 8px; height: 8px; }

        @keyframes floatUp {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) scale(1);
                opacity: 0;
            }
        }

        /* ─── LOADING CARD ────────────────────────────────────────── */
        .loading-container {
            position: relative;
            z-index: 1;
            text-align: center;
            width: min(90%, 700px);
            padding: clamp(1.5rem, 4vw, 3rem);
            animation: fadeInUp 1.2s ease;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ─── LOGO ──────────────────────────────────────────────── */
        .logo {
            font-family: 'Syne', sans-serif;
            font-size: clamp(2.5rem, 7vw, 4.5rem);
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.03em;
            margin-bottom: 0.3rem;
        }

        .logo .highlight {
            color: #e8952a;
        }

        .logo-dot {
            display: inline-block;
            width: 14px;
            height: 14px;
            background: #e8952a;
            border-radius: 50%;
            margin-right: 4px;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.3);
                opacity: 0.7;
            }
        }

        /* ─── TAGLINE ────────────────────────────────────────────── */
        .tagline {
            color: #9c8f7a;
            font-size: 1.1rem;
            font-weight: 300;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        /* ─── LOADING BAR ─────────────────────────────────────────── */
        .loader-wrapper {
            width:100%;
            max-width:350px;
            margin: 0 auto;
        }

        .loader-track {
            width: 100%;
            height: 3px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .loader-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #e8952a, #f5b952);
            border-radius: 4px;
            animation: loadBar 2.5s ease-in-out forwards;
        }

        @keyframes loadBar {
            0% {
                width: 0%;
            }
            20% {
                width: 15%;
            }
            40% {
                width: 35%;
            }
            60% {
                width: 55%;
            }
            80% {
                width: 75%;
            }
            95% {
                width: 92%;
            }
            100% {
                width: 100%;
            }
        }

        .loader-text {
            color: #7a6e5f;
            font-size: 0.75rem;
            font-weight: 400;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 0.7rem;
        }

        /* ─── VERSION / FOOTER ────────────────────────────────────── */
        .version {
            position: fixed;
            bottom: 2rem;
            left: 0;
            right: 0;
            text-align: center;
            color: #4a3c2a;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            z-index: 1;
        }

        /* ─── RESPONSIVE ──────────────────────────────────────────── */
        html,body{width:100%;overflow-x:hidden;}

@media (max-width:768px){
body{padding:20px;}
.loading-container{width:100%;padding:20px;}
.logo{font-size:2.8rem;}
.logo-dot{width:10px;height:10px;}
.tagline{font-size:.9rem;letter-spacing:.08em;}
.loader-wrapper{max-width:100%;}
.loader-text{font-size:.75rem;}
.version{bottom:15px;}
}

@media (max-width:480px){
.logo{font-size:2.2rem;}
.tagline{font-size:.8rem;}
.loader-track{height:4px;}
.loader-text{font-size:.7rem;}
.version{font-size:.65rem;}
}

@media (min-width:769px) and (max-width:1024px){
.loading-container{max-width:600px;}
.logo{font-size:3.5rem;}
}
    </style>
</head>
<body>

    <!-- ─── BACKGROUND PARTICLES ──────────────────────────────── -->
    <div class="bg-particles">
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
    </div>

    <!-- ─── LOADING CONTENT ────────────────────────────────────── -->
    <div class="loading-container">

        <!-- Logo -->
        <div class="logo">
            <span class="logo-dot"></span>
            Bona<span class="highlight">Markets</span>
        </div>

        <!-- Tagline -->
        <p class="tagline">Your trusted African marketplace</p>

        <!-- Loading Bar -->
        <div class="loader-wrapper">
            <div class="loader-track">
                <div class="loader-bar" id="loaderBar"></div>
            </div>
            <p class="loader-text" id="loaderText">Loading experience...</p>
        </div>

    </div>

    <!-- ─── VERSION ──────────────────────────────────────────────── -->
    <div class="version">Bona Markets &bull; v2.0</div>

    <!-- ─── JAVASCRIPT ──────────────────────────────────────────── -->
    <script>
        // ─── LOADING TEXT ROTATION ─────────────────────────────────
        const loadingMessages = [
            'Loading experience...',
            'Connecting to vendors...',
            'Preparing marketplace...',
            'Almost ready...',
            'Welcome to Bona Markets!'
        ];

        let messageIndex = 0;
        const loaderText = document.getElementById('loaderText');

        const messageInterval = setInterval(() => {
            messageIndex++;
            if (messageIndex < loadingMessages.length) {
                loaderText.textContent = loadingMessages[messageIndex];
            }
        }, 500);

        // ─── REDIRECT AFTER LOADING ──────────────────────────────────
        // Redirect to the main homepage after 3.5 seconds
        setTimeout(function() {
            // Clear the interval to stop changing text
            clearInterval(messageInterval);

            // Final message
            loaderText.textContent = 'Welcome to Bona Markets! 🎉';

            // Redirect to the main site
            // Option 1: Redirect to main index
            window.location.href = 'Bona-Markets/Public/index.php';

            // Option 2: If your main site is at a different path, use this instead:
            // window.location.href = '/Bona-Markets/Public/index.php';

            // Option 3: For InfinityFree root path:
            // window.location.href = '/Bona-Markets/Public/index.php';
        }, 3500);

        // ─── FALLBACK: Redirect if JavaScript is disabled ───────────
        // A meta refresh is also added in the HTML head via a noscript tag
    </script>

    <!-- ─── NOSCRIPT FALLBACK ──────────────────────────────────── -->
    <noscript>
        <meta http-equiv="refresh" content="3; url=Bona-Markets/Public/index.php" />
        <style>
            .loader-wrapper { display: none; }
            .loading-container .tagline { margin-bottom: 2rem; }
            .loading-container .logo { margin-bottom: 1rem; }
            .noscript-message {
                color: #9c8f7a;
                font-size: 0.9rem;
                margin-top: 1rem;
            }
        </style>
        <p class="noscript-message">JavaScript is disabled. Redirecting you shortly...</p>
    </noscript>

</body>
</html>