<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseHub TV</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&display=swap');

        html, body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
        }

        *, *::before, *::after { box-sizing: inherit; }

        .tv-container {
            position: relative;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background:
                radial-gradient(circle at center, rgba(0,0,0,0) 60%, rgba(0,0,0,0.7) 100%),
                #0f172a;
            color: #fff;
        }

        .tv-broadcast-banner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #dc2626;
            color: #fff;
            text-align: center;
            font-size: clamp(24px, 4vw, 40px);
            font-weight: bold;
            padding: 16px;
            display: none;
            z-index: 9999;
        }

        #emergencyScreen {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: #dc2626;
            color: #fff;
            font-size: clamp(36px, 6vw, 80px);
            font-weight: bold;
            text-align: center;
            padding: 40px;
            z-index: 10000;
        }

        .tv-screen {
            display: none;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        .tv-layout {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100%;
            min-height: 0;
        }

        .hero-house {
            flex: 0 0 65%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 0;
        }

        .hero-house > .house-card {
            width: 100%;
            height: 100%;
            flex: 1;
            min-height: 0;
        }

        .hero-house .house-card.winner {
            filter: brightness(1.15);
            animation:
                breatheWinner 6s ease-in-out infinite,
                winnerGlow 3s ease-in-out infinite;
        }

        .other-houses {
            flex: 0 0 35%;
            display: flex;
            flex-direction: row;
            min-height: 0;
            width: 100%;
        }

        .mini-house {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: stretch;
            min-width: 0;
            opacity: 0.7;
        }

        .mini-house > .house-card {
            flex: 1;
            width: 100%;
            min-height: 0;
            animation: none;
        }

        .house-card {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 16px;
            min-height: 0;
            min-width: 0;
            transform-origin: center center;
            box-shadow:
                inset 0 0 80px rgba(0,0,0,0.3),
                0 20px 60px rgba(0,0,0,0.5);
            background-image: linear-gradient(
                145deg,
                rgba(255,255,255,0.08),
                rgba(0,0,0,0.2)
            );
            background-color: #334155;
            color: #fff;
            animation: breathe 6s ease-in-out infinite;
        }

        .house-card.gryffindor {
            background-color: #740001;
            color: #fff;
        }
        .house-card.slytherin {
            background-color: #1a472a;
            color: #fff;
        }
        .house-card.ravenclaw {
            background-color: #0e1a40;
            color: #fff;
        }
        .house-card.hufflepuff {
            background-color: #ffcc00;
            color: #111;
        }

        .house-card.winner {
            transform-origin: center center;
            z-index: 2;
            box-shadow:
                inset 0 0 80px rgba(0,0,0,0.3),
                0 20px 60px rgba(0,0,0,0.5),
                0 0 100px rgba(255,255,255,0.25);
            filter: brightness(1.1);
            animation: breatheWinner 6s ease-in-out infinite;
        }

        .house-card .rank {
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 600;
            letter-spacing: 0.05em;
            opacity: 0.6;
            margin-bottom: 8px;
        }

        .house-card .house-name {
            font-size: clamp(28px, 5vw, 56px);
            font-weight: bold;
            letter-spacing: 0.08em;
            text-shadow: 0 4px 12px rgba(0,0,0,0.6);
        }

        .house-card .points {
            font-size: clamp(64px, 14vw, 140px);
            font-weight: 900;
            margin-top: 12px;
            line-height: 1;
            text-shadow: 0 8px 30px rgba(0,0,0,0.8);
        }

        .hero-house .house-card .rank {
            font-size: clamp(28px, 4vw, 42px);
            margin-bottom: 12px;
        }

        .hero-house .house-card .house-name {
            font-size: clamp(40px, 7vw, 80px);
        }

        .hero-house .house-card .points {
            font-size: clamp(100px, 24vw, 260px);
            margin-top: 16px;
        }

        .hero-house .house-name {
            font-weight: 800;
            letter-spacing: 0.1em;
        }

        .hero-house .points {
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .mini-house .house-card .rank {
            font-size: clamp(16px, 2vw, 24px);
            margin-bottom: 6px;
        }

        .mini-house .house-card .house-name {
            font-size: clamp(18px, 3vw, 36px);
        }

        .mini-house .house-card .points {
            font-size: clamp(36px, 8vw, 88px);
            margin-top: 8px;
        }

        .mini-house .house-name {
            font-weight: 600;
        }

        .mini-house .points {
            font-weight: 700;
        }

        @keyframes breathe {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes breatheWinner {
            0%, 100% { transform: scale(1.05); }
            50%      { transform: scale(1.071); }
        }

        @keyframes winnerGlow {
            0% {
                box-shadow:
                    inset 0 0 80px rgba(0,0,0,0.3),
                    0 20px 60px rgba(0,0,0,0.5),
                    0 0 0 rgba(255,255,255,0);
            }
            50% {
                box-shadow:
                    inset 0 0 80px rgba(0,0,0,0.3),
                    0 20px 60px rgba(0,0,0,0.5),
                    0 0 60px rgba(255,255,255,0.25);
            }
            100% {
                box-shadow:
                    inset 0 0 80px rgba(0,0,0,0.3),
                    0 20px 60px rgba(0,0,0,0.5),
                    0 0 0 rgba(255,255,255,0);
            }
        }

        .next-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 10001;
        }

        @keyframes firePulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes streakFadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .streak-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .streak-title {
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 40px;
        }

        .streak-list {
            display: flex;
            flex-direction: column;
            gap: 40px;
        }

        .streak-item {
            display: flex;
            flex-direction: column;
            animation: streakFadeIn 0.6s ease-out;
        }

        .streak-item:nth-child(2) {
            animation-delay: 0.1s;
        }

        .streak-item:nth-child(3) {
            animation-delay: 0.2s;
        }

        .streak-item:first-child {
            transform: scale(1.1);
            opacity: 1;
        }

        .streak-item:not(:first-child) {
            opacity: 0.7;
        }

        .streak-item:first-child .streak-name {
            animation: firePulse 2.5s ease-in-out infinite;
        }

        .streak-name {
            font-size: clamp(36px, 5vw, 70px);
            font-weight: 700;
        }

        .streak-value {
            font-size: clamp(28px, 4vw, 50px);
            font-weight: 900;
            letter-spacing: 0.05em;
            opacity: 0.8;
        }
    </style>
</head>

<body>

<div class="tv-container">

    <div id="emergencyScreen" style="display:none;">
        <div id="emergencyText"></div>
    </div>

    <div id="broadcastBanner" class="tv-broadcast-banner" role="status" aria-live="polite"></div>

    <div class="tv-screen" id="screen-1">
        <div class="tv-layout">

            <div class="hero-house">
                <div class="house-card {{ strtolower($series[0]['name'] ?? 'gryffindor') }} winner">

                    <div class="rank">#1</div>

                    <div class="house-name">
                        {{ strtoupper($series[0]['name']) }}
                    </div>

                    <div class="points" data-points="{{ array_sum($series[0]['data'] ?? []) }}">
                        0
                    </div>

                </div>
            </div>

            <div class="other-houses">
                @foreach($series as $index => $house)
                    @if($index > 0)
                        <div class="mini-house">
                            <div class="house-card {{ strtolower($house['name'] ?? 'gryffindor') }}">

                                <div class="rank">#{{ $index + 1 }}</div>

                                <div class="house-name">
                                    {{ strtoupper($house['name']) }}
                                </div>

                                <div class="points" data-points="{{ array_sum($house['data'] ?? []) }}">
                                    0
                                </div>

                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>

    <div class="tv-screen" id="screen-2">

        <div class="streak-container">

            <div class="streak-title">
                🔥 STREAKS
            </div>

            <div class="streak-list">

                <div class="streak-item">
                    <div class="streak-name">JOSH</div>
                    <div class="streak-value">5 DAY STREAK</div>
                </div>

                <div class="streak-item">
                    <div class="streak-name">EMMA</div>
                    <div class="streak-value">4 DAY STREAK</div>
                </div>

                <div class="streak-item">
                    <div class="streak-name">LIAM</div>
                    <div class="streak-value">3 DAY STREAK</div>
                </div>

            </div>

        </div>

    </div>

    <button type="button" id="nextBtn" class="next-btn">Next ▶</button>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentScreen = 0;
    const screens = document.querySelectorAll('.tv-screen');
    const broadcastUrl = @json(route('broadcast-messages.latest'));
    const broadcastBanner = document.getElementById('broadcastBanner');

    function showScreen(index) {
        screens.forEach((s, i) => {
            s.style.display = (i === index) ? 'block' : 'none';
        });
    }

    function nextScreen() {
        currentScreen = (currentScreen + 1) % screens.length;
        showScreen(currentScreen);
    }

    document.getElementById('nextBtn').addEventListener('click', nextScreen);

    function animatePoints() {
        document.querySelectorAll('.points').forEach(el => {
            const target = parseInt(el.dataset.points || 0);
            let current = 0;

            const increment = Math.max(1, Math.ceil(target / 40));

            const interval = setInterval(() => {
                current += increment;

                if (current >= target) {
                    el.innerText = target;
                    clearInterval(interval);
                } else {
                    el.innerText = current;
                }
            }, 20);
        });
    }

    showScreen(0);
    animatePoints();

    setTimeout(() => {
        showScreen(0);
    }, 100);

    function fetchBroadcast() {
        const emergencyScreen = document.getElementById('emergencyScreen');
        const emergencyText = document.getElementById('emergencyText');

        fetch(broadcastUrl)
            .then(function (res) {
                if (!res.ok) throw new Error('broadcast fetch failed');
                return res.json();
            })
            .then(function (data) {
                const message = data && data.message ? String(data.message) : '';

                if (message && message.startsWith('EMERGENCY:')) {
                    if (emergencyScreen) {
                        emergencyScreen.style.display = 'flex';
                    }
                    if (emergencyText) {
                        emergencyText.innerText = message.slice('EMERGENCY:'.length).trim();
                    }
                    if (broadcastBanner) {
                        broadcastBanner.style.display = 'none';
                    }
                } else {
                    if (emergencyScreen) {
                        emergencyScreen.style.display = 'none';
                    }
                    if (broadcastBanner) {
                        if (message) {
                            broadcastBanner.textContent = message;
                            broadcastBanner.style.display = 'block';
                        } else {
                            broadcastBanner.style.display = 'none';
                        }
                    }
                }
            })
            .catch(function () {
                if (broadcastBanner) {
                    broadcastBanner.style.display = 'none';
                }
            });
    }

    fetchBroadcast();
    setInterval(fetchBroadcast, 5000);
});
</script>

</body>
</html>
