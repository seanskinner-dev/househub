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
            flex-direction: column;
            box-sizing: border-box;
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
            gap: 28px;
        }

        .streak-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }

        .streak-sep {
            opacity: 0.5;
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

        .streak-item.gryffindor .streak-name {
            color: #ff4d4d;
        }

        .streak-item.slytherin .streak-name {
            color: #4ade80;
        }

        .streak-item.ravenclaw .streak-name {
            color: #60a5fa;
        }

        .streak-item.hufflepuff .streak-name {
            color: #facc15;
        }

        .streak-item:first-child.gryffindor .streak-name {
            text-shadow: 0 0 20px rgba(255, 77, 77, 0.6);
        }

        .streak-item:first-child.slytherin .streak-name {
            text-shadow: 0 0 20px rgba(74, 222, 128, 0.6);
        }

        .streak-item:first-child.ravenclaw .streak-name {
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.6);
        }

        .streak-item:first-child.hufflepuff .streak-name {
            text-shadow: 0 0 20px rgba(250, 204, 21, 0.6);
        }

        .activity-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .activity-title {
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 40px;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: clamp(32px, 5vw, 60px);
            font-weight: 700;
        }

        .activity-points {
            font-weight: 900;
        }

        .activity-sep {
            opacity: 0.5;
        }

        .activity-item.gryffindor .activity-house {
            color: #ff4d4d;
        }

        .activity-item.slytherin .activity-house {
            color: #4ade80;
        }

        .activity-item.ravenclaw .activity-house {
            color: #60a5fa;
        }

        .activity-item.hufflepuff .activity-house {
            color: #facc15;
        }

        .top-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .top-title {
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 40px;
        }

        .top-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .top-item {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: clamp(30px, 4.5vw, 55px);
            font-weight: 700;
        }

        .top-rank {
            opacity: 0.6;
        }

        .top-sep {
            opacity: 0.5;
        }

        .top-points {
            font-weight: 900;
        }

        .top-item.gryffindor .top-name {
            color: #ff4d4d;
        }

        .top-item.slytherin .top-name {
            color: #4ade80;
        }

        .top-item.ravenclaw .top-name {
            color: #60a5fa;
        }

        .top-item.hufflepuff .top-name {
            color: #facc15;
        }

        .weather-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 24px 16px 16px;
            min-height: 0;
            overflow: hidden;
            animation: weatherFade 1s ease;
        }

        .weather-hero {
            position: relative;
            overflow: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 0;
        }

        .weather-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background-size: cover;
            background-position: center;
            transition: all 1s ease;
            filter: blur(4px) brightness(0.9);
            animation: skyDrift 60s linear infinite;
        }

        .weather-hero.sun-active::before {
            background-image: url('https://images.unsplash.com/photo-1502082553048-f009c37129b9');
        }

        .weather-hero.cloud-active::before {
            background-image: url('https://images.unsplash.com/photo-1499346030926-9a72daac6c63');
        }

        .weather-hero.rain-active::before {
            background-image: url('https://images.unsplash.com/photo-1501594907352-04cda38ebc29');
        }

        .weather-hero.storm-active::before {
            background-image: url('https://images.unsplash.com/photo-1500673922987-e212871fec22');
            animation: skyDrift 60s linear infinite, stormFlash 6s infinite;
        }

        .weather-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.2),
                rgba(0, 0, 0, 0.6)
            );
            z-index: 0;
        }

        .weather-hero.sun-active::after {
            background: radial-gradient(
                circle at 50% 30%,
                rgba(255, 223, 100, 0.25),
                rgba(0, 0, 0, 0.6)
            );
        }

        .weather-hero.rain-active::after {
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.4),
                rgba(0, 0, 0, 0.7)
            );
        }

        .weather-hero.rain-active {
            filter: contrast(0.9) brightness(0.8);
        }

        .weather-hero > * {
            position: relative;
            z-index: 1;
        }

        .weather-scene {
            height: 200px;
            position: relative;
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
            transform: scale(0.9);
        }

        .sun {
            display: none;
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #facc15;
            box-shadow: 0 0 80px rgba(250, 204, 21, 0.7);
            animation: weatherSunPulse 6s ease-in-out infinite;
        }

        .weather-hero.sun-active .sun {
            display: block;
        }

        @keyframes weatherSunPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.06); }
        }

        .cloud {
            display: none;
            width: 180px;
            height: 90px;
            background: #e2e8f0;
            border-radius: 50px;
            opacity: 0.6;
            animation: cloudFloat 20s ease-in-out infinite;
        }

        .weather-hero.cloud-active .cloud,
        .weather-hero.rain-active .cloud,
        .weather-hero.storm-active .cloud {
            display: block;
        }

        @keyframes cloudFloat {
            0% { transform: translateX(-20px); }
            50% { transform: translateX(20px); }
            100% { transform: translateX(-20px); }
        }

        .rain {
            display: none;
            width: 120px;
            height: 120px;
            position: relative;
        }

        .weather-hero.rain-active .rain,
        .weather-hero.storm-active .rain {
            display: block;
        }

        .rain::before {
            content: "";
            position: absolute;
            left: 50%;
            top: 0;
            width: 2px;
            height: 100%;
            margin-left: -1px;
            background: repeating-linear-gradient(
                transparent,
                transparent 6px,
                #60a5fa 6px,
                #60a5fa 12px
            );
            animation: weatherRainFall 1s linear infinite;
            opacity: 0.85;
        }

        @keyframes weatherRainFall {
            0% { transform: translateY(-12px); }
            100% { transform: translateY(12px); }
        }

        .lightning {
            display: none;
            align-items: center;
            justify-content: center;
            min-width: 120px;
            min-height: 120px;
        }

        .weather-hero.storm-active .lightning {
            display: flex;
        }

        .lightning::before {
            content: "⚡";
            font-size: 100px;
            line-height: 1;
            opacity: 0;
            animation: weatherLightningFlash 4s infinite;
        }

        @keyframes weatherLightningFlash {
            0%, 88%, 100% { opacity: 0; }
            90% { opacity: 1; }
            92% { opacity: 0; }
            94% { opacity: 1; }
        }

        .weather-main {
            width: 100%;
            max-width: 960px;
            backdrop-filter: blur(6px);
            padding: 20px 40px;
            border-radius: 20px;
        }

        .weather-temp-main {
            font-size: clamp(120px, 20vw, 260px);
            font-weight: 900;
            text-align: center;
            line-height: 1;
            letter-spacing: -0.02em;
            text-shadow:
                0 10px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.2);
        }

        .weather-description {
            font-size: clamp(28px, 4vw, 50px);
            text-align: center;
            opacity: 0.8;
            margin-top: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .weather-secondary {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 12px 8px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            margin: 0 40px 20px;
            padding: 20px;
            font-size: clamp(22px, 3vw, 36px);
            opacity: 0.6;
            flex-shrink: 0;
        }

        .weather-mini {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .weather-mini span:first-child {
            font-weight: 600;
            opacity: 0.85;
        }

        .weather-mini span:last-child {
            font-weight: 800;
        }

        @keyframes skyDrift {
            0% { transform: scale(1.05) translateX(0); }
            50% { transform: scale(1.05) translateX(-10px); }
            100% { transform: scale(1.05) translateX(0); }
        }

        @keyframes stormFlash {
            0%, 96%, 100% { filter: blur(4px) brightness(0.9); }
            97% { filter: blur(4px) brightness(2.2); }
            98% { filter: blur(4px) brightness(0.9); }
        }

        @keyframes weatherFade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                <div class="house-card {{ strtolower(str_replace(' ', '', $series[0]['name'] ?? 'gryffindor')) }} winner">

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
                            <div class="house-card {{ strtolower(str_replace(' ', '', $house['name'] ?? 'gryffindor')) }}">

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

                <div class="streak-item gryffindor">
                    <div class="streak-line">
                        <span class="streak-name">🦁 JOSH</span>
                        <span class="streak-sep">—</span>
                        <span class="streak-value">5 DAY STREAK</span>
                    </div>
                </div>

                <div class="streak-item slytherin">
                    <div class="streak-line">
                        <span class="streak-name">🐍 EMMA</span>
                        <span class="streak-sep">—</span>
                        <span class="streak-value">4 DAY STREAK</span>
                    </div>
                </div>

                <div class="streak-item ravenclaw">
                    <div class="streak-line">
                        <span class="streak-name">🦅 LIAM</span>
                        <span class="streak-sep">—</span>
                        <span class="streak-value">3 DAY STREAK</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-3">

        <div class="activity-container">

            <div class="activity-title">
                ⚡ LIVE ACTIVITY
            </div>

            <div class="activity-list">

                <div class="activity-item gryffindor">
                    <span class="activity-points">+1</span>
                    <span class="activity-house">Gryffindor</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Josh</span>
                </div>

                <div class="activity-item slytherin">
                    <span class="activity-points">+5</span>
                    <span class="activity-house">Slytherin</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Emma</span>
                </div>

                <div class="activity-item ravenclaw">
                    <span class="activity-points">+1</span>
                    <span class="activity-house">Ravenclaw</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Liam</span>
                </div>

            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-4">

        <div class="top-container">

            <div class="top-title">
                🏆 TOP STUDENTS
            </div>

            <div class="top-list">

                <div class="top-item gryffindor">
                    <span class="top-rank">1.</span>
                    <span class="top-name">🦁 JOSH</span>
                    <span class="top-sep">—</span>
                    <span class="top-points">120 pts</span>
                </div>

                <div class="top-item slytherin">
                    <span class="top-rank">2.</span>
                    <span class="top-name">🐍 EMMA</span>
                    <span class="top-sep">—</span>
                    <span class="top-points">110 pts</span>
                </div>

                <div class="top-item ravenclaw">
                    <span class="top-rank">3.</span>
                    <span class="top-name">🦅 LIAM</span>
                    <span class="top-sep">—</span>
                    <span class="top-points">95 pts</span>
                </div>

                <div class="top-item hufflepuff">
                    <span class="top-rank">4.</span>
                    <span class="top-name">🦡 OLIVIA</span>
                    <span class="top-sep">—</span>
                    <span class="top-points">90 pts</span>
                </div>

                <div class="top-item gryffindor">
                    <span class="top-rank">5.</span>
                    <span class="top-name">🦁 NOAH</span>
                    <span class="top-sep">—</span>
                    <span class="top-points">85 pts</span>
                </div>

            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-weather">

        @php
            $maxRain = max(array_column($weather, 'rain'));
            $maxCode = max(array_column($weather, 'code'));
        @endphp

        <div class="weather-container">

            <div class="weather-hero
                @if($maxCode >= 95 || $maxRain >= 60)
                    storm-active
                @elseif($maxRain >= 40)
                    rain-active
                @elseif($maxCode >= 2)
                    cloud-active
                @else
                    sun-active
                @endif
            ">

                <div class="weather-scene">

                    <div class="sun"></div>
                    <div class="cloud"></div>
                    <div class="rain"></div>
                    <div class="lightning"></div>

                </div>

                <div class="weather-main">

                    <div class="weather-temp-main">
                        {{ $weather[1]['temp'] }}°
                    </div>

                    <div class="weather-description">
                        @if($maxCode >= 95 || $maxRain >= 60)
                            Storm Conditions
                        @elseif($maxRain >= 40)
                            Rain Expected
                        @elseif($maxCode >= 2)
                            Cloudy
                        @else
                            Clear Skies
                        @endif
                    </div>

                </div>

            </div>

            <div class="weather-secondary">

                @foreach($weather as $slot)

                    <div class="weather-mini">
                        <span>{{ $slot['label'] }}</span>
                        <span>{{ $slot['temp'] }}°</span>
                    </div>

                @endforeach

            </div>

        </div>

    </div>

    <button type="button" id="nextBtn" class="next-btn">Next ▶</button>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentScreen = 0;
    const screens = document.querySelectorAll('.tv-screen');
    console.log('TV screens found:', screens.length);
    const broadcastUrl = @json(route('broadcast-messages.latest'));
    const broadcastBanner = document.getElementById('broadcastBanner');

    function showScreen(index) {
        screens.forEach((s, i) => {
            s.style.display = (i === index) ? 'flex' : 'none';
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

<script>
setInterval(function () {
    window.location.reload();
}, 300000);
</script>

</body>
</html>
