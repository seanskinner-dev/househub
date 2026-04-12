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
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
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
            position: relative;
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 40px;
            overflow: hidden;
        }

        .activity-title::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            animation: shimmer 3s infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
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
            opacity: 0;
            transform: translateY(20px);
            animation: activityFadeIn 0.6s ease forwards;
        }

        .activity-item:nth-child(1) { animation-delay: 0.1s; }
        .activity-item:nth-child(2) { animation-delay: 0.2s; }
        .activity-item:nth-child(3) { animation-delay: 0.3s; }

        @keyframes activityFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .activity-item strong {
            animation: pulsePoints 2.5s ease-in-out infinite;
        }

        @keyframes pulsePoints {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .activity-points {
            font-weight: 900;
        }

        .activity-sep {
            opacity: 0.5;
        }

        .house {
            font-weight: 700;
        }

        .house.gryffindor { color: #ef4444; }
        .house.slytherin { color: #22c55e; }
        .house.ravenclaw { color: #60a5fa; }
        .house.hufflepuff { color: #facc15; }

        .top-container {
            flex: 1;
            min-height: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-sizing: border-box;
            padding: 12px 20px;
        }

        .top-title {
            flex-shrink: 0;
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 20px;
        }

        .top-hero {
            flex-shrink: 0;
            text-align: center;
            color: #fff;
            margin-bottom: 10px;
        }

        .top-hero .crown {
            font-size: 60px;
            line-height: 1;
            margin: 0 auto 10px;
            display: block;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.35));
            animation: crownFloat 3s ease-in-out infinite;
        }

        @keyframes crownFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .top-hero .hero-name {
            font-size: clamp(70px, 8vw, 120px);
            line-height: 1;
            font-weight: 900;
            text-shadow:
                0 0 30px rgba(255, 255, 255, 0.2),
                0 0 60px rgba(255, 215, 0, 0.4);
            animation: goldPulse 3s ease-in-out infinite;
        }

        .top-hero .hero-points {
            font-size: clamp(30px, 4vw, 60px);
            font-weight: 700;
            opacity: 0.8;
            margin-top: 5px;
        }

        @keyframes goldPulse {
            0%, 100% {
                text-shadow:
                    0 0 20px rgba(255, 215, 0, 0.4),
                    0 0 40px rgba(255, 215, 0, 0.3);
            }
            50% {
                text-shadow:
                    0 0 40px rgba(255, 215, 0, 0.8),
                    0 0 80px rgba(255, 215, 0, 0.6);
            }
        }

        .top-list {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            width: 100%;
            max-width: 1100px;
            overflow: hidden;
        }

        .student-row {
            font-size: clamp(26px, 3vw, 44px);
            line-height: 1.1;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease forwards;
            transition: opacity 0.3s;
        }

        .student-row:nth-child(1) { animation-delay: 0.1s; }
        .student-row:nth-child(2) { animation-delay: 0.2s; }
        .student-row:nth-child(3) { animation-delay: 0.3s; }
        .student-row:nth-child(4) { animation-delay: 0.4s; }

        .student-row:hover {
            opacity: 1;
        }

        @keyframes fadeUp {
            to {
                opacity: 0.7;
                transform: translateY(0);
            }
        }

        .top-item {
            display: flex;
            align-items: center;
            gap: 16px;
            font-weight: 700;
        }

        .student-row.top-item {
            font-size: clamp(26px, 3vw, 44px);
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
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 0;
        }

        .weather-main {
            width: 100%;
            max-width: 960px;
            padding: 20px 40px;
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
            font-weight: 700;
            margin-top: 12px;
            letter-spacing: 0.04em;
            transition: all 0.5s ease;
        }

        .weather-good {
            color: #4ade80;
            text-shadow: 0 0 20px rgba(74, 222, 128, 0.4);
        }

        .weather-warning {
            color: #facc15;
            text-shadow: 0 0 20px rgba(250, 204, 21, 0.4);
        }

        .weather-bad {
            color: #f87171;
            text-shadow: 0 0 20px rgba(248, 113, 113, 0.4);
        }

        .weather-breaks {
            display: flex;
            justify-content: center;
            gap: 80px;
            margin-top: 40px;
            flex-shrink: 0;
        }

        .weather-break {
            font-size: clamp(28px, 4vw, 48px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            opacity: 0.9;
        }

        .weather-break span:first-child {
            font-weight: 600;
            opacity: 0.7;
        }

        .weather-break .rain {
            color: #60a5fa;
            text-shadow: 0 0 20px rgba(96, 165, 250, 0.4);
        }

        .weather-break .dry {
            color: #facc15;
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
                    <strong class="activity-points">+1</strong>
                    <span class="house gryffindor">🦁 Gryffindor</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Josh</span>
                </div>

                <div class="activity-item slytherin">
                    <strong class="activity-points">+5</strong>
                    <span class="house slytherin">🐍 Slytherin</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Emma</span>
                </div>

                <div class="activity-item ravenclaw">
                    <strong class="activity-points">+1</strong>
                    <span class="house ravenclaw">🦅 Ravenclaw</span>
                    <span class="activity-sep">—</span>
                    <span class="activity-student">Liam</span>
                </div>

            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-4">

        <div class="top-container">

            @php
                $leader = $topStudents[0] ?? null;
                $list = $topStudents->slice(1, 4)->values();
            @endphp

            <div class="top-title">
                🏆 TOP STUDENTS
            </div>

            <div style="position:absolute; top:100px; left:20px; color:white; font-size:20px; z-index:9999;">
                <strong>DEBUG:</strong><br>

                @foreach($topStudents as $i => $s)
                    {{ $i }} — {{ $s->name }} — {{ $s->house_points }} pts<br>
                @endforeach
            </div>

            @if($leader)
                <div class="top-hero">
                    <div class="crown">👑</div>
                    <div class="hero-name">
                        {{ $leader->name }}
                    </div>
                    <div class="hero-points">
                        {{ $leader->house_points }} pts
                    </div>
                </div>
            @endif

            <div class="top-list">
                @foreach($list as $index => $student)
                    <div class="student-row">
                        {{ $index + 2 }}.
                        {{ $student->name }} — {{ $student->house_points }} pts
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-weather">

        <div class="weather-container">

            @php
                $maxRain = max(array_column($weather, 'rain'));
                $recessRain = (collect($weather)->firstWhere('label', 'RECESS') ?? [])['rain'] ?? 0;
                $lunchRain = (collect($weather)->firstWhere('label', 'LUNCH') ?? [])['rain'] ?? 0;

                $rainThreshold = 40;
                $heavyThreshold = 70;

                $desc = '';
                $severityClass = 'weather-good';

                if ($recessRain >= $rainThreshold && $lunchRain >= $rainThreshold) {
                    $desc = 'Rain at Recess & Lunch';
                    $severityClass = ($recessRain >= $heavyThreshold || $lunchRain >= $heavyThreshold)
                        ? 'weather-bad'
                        : 'weather-warning';
                } elseif ($recessRain >= $rainThreshold) {
                    $desc = 'Rain at Recess';
                    $severityClass = ($recessRain >= $heavyThreshold)
                        ? 'weather-bad'
                        : 'weather-warning';
                } elseif ($lunchRain >= $rainThreshold) {
                    $desc = 'Rain at Lunch';
                    $severityClass = ($lunchRain >= $heavyThreshold)
                        ? 'weather-bad'
                        : 'weather-warning';
                } elseif ($maxRain >= $rainThreshold) {
                    $desc = 'Showers Today';
                    $severityClass = 'weather-warning';
                } else {
                    $desc = 'No Rain During Breaks';
                    $severityClass = 'weather-good';
                }
            @endphp

            <div class="weather-hero">

                <div class="weather-main">

                    <div class="weather-temp-main">
                        {{ $weather[1]['temp'] }}°
                    </div>

                    <div class="weather-description {{ $severityClass }}">
                        {{ $desc }}
                    </div>

                </div>

            </div>

            <div class="weather-breaks">

                @php
                    $recessRain = (collect($weather)->firstWhere('label', 'RECESS') ?? [])['rain'] ?? 0;
                    $lunchRain = (collect($weather)->firstWhere('label', 'LUNCH') ?? [])['rain'] ?? 0;
                @endphp

                <div class="weather-break">
                    <span>RECESS</span>
                    <span class="{{ $recessRain >= 40 ? 'rain' : 'dry' }}">
                        {{ $recessRain >= 40 ? '🌧' : '☀️' }}
                    </span>
                </div>

                <div class="weather-break">
                    <span>LUNCH</span>
                    <span class="{{ $lunchRain >= 40 ? 'rain' : 'dry' }}">
                        {{ $lunchRain >= 40 ? '🌧' : '☀️' }}
                    </span>
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
