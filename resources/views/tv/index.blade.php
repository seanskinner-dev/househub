<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseHub TV</title>
    <style>
        body {
            margin: 0;
        }

/* BASE */
.tv-container {
    height: 100vh;
    width: 100vw;
    background: #0f172a;
    color: white;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

.tv-broadcast-banner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #dc2626;
    color: white;
    text-align: center;
    font-size: 40px;
    font-weight: bold;
    padding: 20px;
    display: none;
    z-index: 9999;
}

#emergencyScreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: #dc2626;
    color: white;

    align-items: center;
    justify-content: center;

    font-size: 80px;
    font-weight: bold;
    text-align: center;

    z-index: 10000;
    padding: 40px;
}

/* FIXED SCREEN HEIGHT */
.tv-screen {
    display: none;
    height: 100vh;
    width: 100%;
}

.house-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    height: 100vh;
    width: 100vw;
}

.house-card {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: stretch;
    color: white;
    min-height: 0;

    transition: box-shadow 0.4s ease;
    animation: fadeIn 0.8s ease forwards;
}

.house-card:hover {
    filter: brightness(1.06);
}

.house-card.winner {
    box-shadow: 0 0 60px rgba(255,255,255,0.5);
    animation: fadeIn 0.8s ease forwards, pulse 2s ease-in-out 0.8s infinite;
}

.house-card-inner {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    min-height: 0;
    animation: float 6s ease-in-out infinite;
}

.house-card .house-name {
    font-size: 48px;
    font-weight: bold;
    letter-spacing: 2px;
}

.house-card .points {
    font-size: 140px;
    font-weight: bold;
    margin: 20px 0;
    text-shadow: 0 0 20px rgba(255,255,255,0.4);
}

.house-card .rank {
    position: absolute;
    top: 20px;
    left: 20px;
    font-size: 28px;
    background: rgba(0,0,0,0.4);
    padding: 6px 12px;
    border-radius: 6px;
    opacity: 0.9;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
}

@keyframes pulse {
    0% { transform: scale(1.05); }
    50% { transform: scale(1.08); }
    100% { transform: scale(1.05); }
}

.next-btn {
    position: absolute;
    bottom: 20px;
    right: 20px;
}

    </style>
</head>

<body>

<div class="tv-container">

    <div id="emergencyScreen" style="display:none;">
        <div id="emergencyText"></div>
    </div>

    <div id="broadcastBanner" class="tv-broadcast-banner" role="status" aria-live="polite"></div>

    <!-- SCREEN 1 -->
    <div class="tv-screen" id="screen-1">
        <div class="house-grid">
            @foreach($series as $index => $house)
                <div class="house-card {{ $index === 0 ? 'winner' : '' }}"
                     style="background: linear-gradient(135deg, {{ $house['color'] }}, #000000)">

                    <div class="house-card-inner">

                        <div class="rank">#{{ $index + 1 }}</div>

                        <div class="house-name">
                            {{ strtoupper($house['name']) }}
                        </div>

                        <div class="points" data-points="{{ array_sum($house['data'] ?? []) }}">
                            0
                        </div>

                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <button id="nextBtn" class="next-btn">Next ▶</button>

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
