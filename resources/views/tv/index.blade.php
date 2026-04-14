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
            background-color: #3b82f6;
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

        .streak-list {
            width: min(1200px, 92vw);
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 40px;
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
            width: min(1200px, 92vw);
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 40px;
        }

        @keyframes activityFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        .top-list {
            gap: 14px;
            width: min(1200px, 92vw);
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

        .tv-this-term-screen {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 0;
            width: 100%;
            padding: 20px 24px 28px;
            box-sizing: border-box;
        }

        .tv-this-term-title {
            flex-shrink: 0;
            font-size: clamp(36px, 5vw, 72px);
            font-weight: 800;
            text-align: center;
            margin-bottom: 20px;
            letter-spacing: 0.02em;
        }

        .tv-this-term-grid {
            flex: 1;
            min-height: 0;
            width: 100%;
            max-width: 1280px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 16px;
        }

        .this-term-card {
            animation: none;
            color: #fff !important;
            justify-content: center;
            padding: clamp(16px, 2.5vw, 28px);
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.55);
        }

        .this-term-card .rank {
            font-size: clamp(24px, 3.2vw, 40px);
            font-weight: 700;
            opacity: 0.85;
            margin-bottom: 10px;
        }

        .this-term-card .house-name {
            font-size: clamp(32px, 5vw, 64px);
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .this-term-card .points {
            font-size: clamp(72px, 14vw, 160px);
            font-weight: 900;
            margin-top: 16px;
            line-height: 1;
        }

        .screen-inner {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            padding: 20px 30px;
            background: #0f172a;
            color: #ffffff;
        }

        .screen-title {
            font-size: 3rem;
            font-weight: 900;
            margin: 0;
            margin-top: 10px;
            text-align: center;
            letter-spacing: 1px;
        }

        .leaderboard-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 20px;
            padding: 0 40px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            flex: 1;
            justify-content: center;
        }

        .leaderboard-list .student-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-radius: 16px;
            background: linear-gradient(
                135deg,
                rgba(30, 41, 59, 0.9),
                rgba(2, 6, 23, 0.95)
            );
            font-size: 1.9rem;
            font-weight: 700;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 10px 30px rgba(0,0,0,0.6),
                0 0 20px rgba(255,255,255,0.03),
                0 0 25px var(--house-color),
                inset 0 1px 0 rgba(255,255,255,0.05);
            backdrop-filter: blur(6px);
        }

        .student-card::before {
            content: "";
            position: absolute;
            left: -60px;
            top: 0;
            width: 160px;
            height: 100%;
            background: radial-gradient(circle, var(--house-color), transparent 70%);
            opacity: 0.35;
        }

        .leaderboard-list .student-card:first-child {
            transform: scale(1.03);
            background: linear-gradient(
                135deg,
                rgba(255,255,255,0.08),
                rgba(2, 6, 23, 1)
            );
            box-shadow:
                0 0 40px var(--house-color),
                0 12px 40px rgba(0,0,0,0.8);
        }

        .student-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .leaderboard-list .student-emoji {
            font-size: 1.6rem;
        }

        .leaderboard-list .student-name {
            font-size: 2rem;
            letter-spacing: 0.5px;
        }

        .leaderboard-list .student-rank {
            font-size: 1.3rem;
            opacity: 0.6;
        }

        .screen-inner.gryffindor,
        .screen-inner.slytherin,
        .screen-inner.ravenclaw,
        .screen-inner.hufflepuff { background: #0f172a; color: #fff; }
    </style>
</head>

<body>

<div class="tv-container">
    @php
        if (!function_exists('houseMeta')) {
            function houseMeta($house) {
                return match($house) {
                    'Gryffindor' => ['color' => '#740001', 'emoji' => '🦁'],
                    'Slytherin' => ['color' => '#1a472a', 'emoji' => '🐍'],
                    'Ravenclaw' => ['color' => '#3b82f6', 'emoji' => '🦅'],
                    'Hufflepuff' => ['color' => '#ffcc00', 'emoji' => '🦡'],
                    default => ['color' => '#444', 'emoji' => '🏫'],
                };
            }
        }
    @endphp

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
                @php
                    $streakData = collect([
                        ['student_name' => 'JOSH', 'house_name' => 'Gryffindor', 'days' => 6],
                        ['student_name' => 'EMMA', 'house_name' => 'Slytherin', 'days' => 5],
                        ['student_name' => 'LIAM', 'house_name' => 'Ravenclaw', 'days' => 4],
                        ['student_name' => 'AVA', 'house_name' => 'Hufflepuff', 'days' => 3],
                        ['student_name' => 'NOAH', 'house_name' => 'Gryffindor', 'days' => 2],
                        ['student_name' => 'MIA', 'house_name' => 'Ravenclaw', 'days' => 2],
                    ])->take(6);
                @endphp
                @foreach($streakData as $streak)
                    @php
                        $meta = houseMeta($streak['house_name']);
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $streak['student_name'] }}</span>
                        </div>
                        <div class="student-rank">{{ $streak['days'] }} DAY STREAK</div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-3">

        <div class="activity-container">

            <div class="activity-title">
                ⚡ LIVE ACTIVITY
            </div>

            <div class="activity-list">
                @php
                    $activityData = collect([
                        ['student_name' => 'JOSH', 'house_name' => 'Gryffindor', 'points' => 6],
                        ['student_name' => 'EMMA', 'house_name' => 'Slytherin', 'points' => 5],
                        ['student_name' => 'LIAM', 'house_name' => 'Ravenclaw', 'points' => 4],
                        ['student_name' => 'AVA', 'house_name' => 'Hufflepuff', 'points' => 3],
                        ['student_name' => 'NOAH', 'house_name' => 'Gryffindor', 'points' => 2],
                        ['student_name' => 'MIA', 'house_name' => 'Ravenclaw', 'points' => 1],
                    ])->take(6);
                @endphp
                @foreach($activityData as $activity)
                    @php
                        $meta = houseMeta($activity['house_name']);
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $activity['student_name'] }}</span>
                        </div>
                        <div class="student-rank">+{{ $activity['points'] }}</div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-4">

        <div class="top-container">

            @php
                $leader = $topStudents[0] ?? null;
                $list = $topStudents->slice(1, 9)->values();
            @endphp

            <div class="top-title">
                🏆 TOP STUDENTS
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
                    @php
                        $houseName = optional($student->house)->name ?? ($student->house_name ?? null);
                        $meta = houseMeta($houseName);
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $student->name }}</span>
                        </div>
                        <div class="student-rank">#{{ $index + 2 }}</div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-house-points-this-term">

        <div class="tv-this-term-screen">

            <div class="tv-this-term-title">
                House Points - This Term
            </div>

            <div class="tv-this-term-grid">
                @foreach($housePointsThisTerm as $entry)
                    <div
                        class="house-card this-term-card"
                        style="background-color: {{ $entry['colour_hex'] }};"
                    >
                        <div class="rank">#{{ $loop->iteration }}</div>
                        <div class="house-name">{{ strtoupper($entry['house']) }}</div>
                        <div class="points" data-points="{{ $entry['total'] }}">0</div>
                    </div>
                @endforeach
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-house-points-this-year">

        <div class="tv-this-term-screen">

            <div class="tv-this-term-title">
                House Points - This Year
            </div>

            <div class="tv-this-term-grid">
                @foreach(collect($housePointsThisYear)->take(4) as $entry)
                    <div
                        class="house-card this-term-card"
                        style="background-color: {{ $entry['colour_hex'] }};"
                    >
                        <div class="rank">#{{ $loop->iteration }}</div>
                        <div class="house-name">{{ strtoupper($entry['house']) }}</div>
                        <div class="points" data-points="{{ $entry['total'] }}">0</div>
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

    <div class="tv-screen" id="screen-top-gryffindor">
        <div class="screen-inner gryffindor">
            <h1 class="screen-title">Top 10 - Gryffindor</h1>
            <div class="leaderboard-list">
                @foreach($topGryffindor as $index => $student)
                    @php
                        $meta = houseMeta($student->house_name ?? 'Gryffindor');
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </div>
                        <div class="student-rank">#{{ $index + 1 }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-slytherin">
        <div class="screen-inner slytherin">
            <h1 class="screen-title">Top 10 - Slytherin</h1>
            <div class="leaderboard-list">
                @foreach($topSlytherin as $index => $student)
                    @php
                        $meta = houseMeta($student->house_name ?? 'Slytherin');
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </div>
                        <div class="student-rank">#{{ $index + 1 }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-ravenclaw">
        <div class="screen-inner ravenclaw">
            <h1 class="screen-title">Top 10 - Ravenclaw</h1>
            <div class="leaderboard-list">
                @foreach($topRavenclaw as $index => $student)
                    @php
                        $meta = houseMeta($student->house_name ?? 'Ravenclaw');
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </div>
                        <div class="student-rank">#{{ $index + 1 }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-hufflepuff">
        <div class="screen-inner hufflepuff">
            <h1 class="screen-title">Top 10 - Hufflepuff</h1>
            <div class="leaderboard-list">
                @foreach($topHufflepuff as $index => $student)
                    @php
                        $meta = houseMeta($student->house_name ?? 'Hufflepuff');
                    @endphp
                    <div class="student-card" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                        </div>
                        <div class="student-rank">#{{ $index + 1 }}</div>
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
    let ommExpiryTimeout = null;

    function shuffle(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    const screens = Array.from(document.querySelectorAll('.tv-screen'));
    shuffle(screens);
    console.log('TV screens found:', screens.length);
    const broadcastUrl = @json(route('broadcast-messages.latest'));
    const broadcastBanner = document.getElementById('broadcastBanner');

    function showScreen(index) {
        screens.forEach((s, i) => {
            s.style.display = (i === index) ? 'flex' : 'none';
        });
    }

    function nextScreen() {
        currentScreen++;
        if (currentScreen >= screens.length) {
            shuffle(screens);
            currentScreen = 0;
        }
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
                const expiresAt = data && data.expires_at ? String(data.expires_at) : '';

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

                if (ommExpiryTimeout) {
                    clearTimeout(ommExpiryTimeout);
                    ommExpiryTimeout = null;
                }
                if (expiresAt) {
                    const expiryTime = new Date(expiresAt).getTime();
                    const nowMs = new Date().getTime();
                    const timeLeft = expiryTime - nowMs;
                    if (timeLeft > 0) {
                        ommExpiryTimeout = setTimeout(function () {
                            if (broadcastBanner) {
                                broadcastBanner.style.display = 'none';
                            }
                            if (emergencyScreen) {
                                emergencyScreen.style.display = 'none';
                            }
                        }, timeLeft);
                    } else {
                        if (broadcastBanner) {
                            broadcastBanner.style.display = 'none';
                        }
                        if (emergencyScreen) {
                            emergencyScreen.style.display = 'none';
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
