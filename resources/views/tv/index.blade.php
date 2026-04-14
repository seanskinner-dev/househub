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
            background: #0a0a0a;
            color: #fff;
            transition: background 1s ease;
            animation: bgDrift 20s ease-in-out infinite;
        }

        .tv-container::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.25);
            pointer-events: none;
            z-index: 0;
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
            flex-direction: column;
            gap: 18px;
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

        #emergencyScreen .em-sub {
            font-size: clamp(18px, 2.2vw, 32px);
            font-weight: 700;
            opacity: 0.95;
        }

        .tv-screen {
            display: none;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
            background: transparent !important;
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

        .streak-list {
            width: min(1240px, 95vw);
            margin-top: 16px;
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
            width: min(1240px, 95vw);
            margin-top: 16px;
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
            padding: 18px 28px;
            background: transparent;
            color: #fff;
        }

        .top-title {
            flex-shrink: 0;
            font-size: clamp(40px, 6vw, 80px);
            font-weight: 800;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 0.04em;
            text-shadow: 0 8px 28px rgba(0, 0, 0, 0.6);
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
            width: min(1240px, 95vw);
            margin-top: 16px;
        }

        .weather-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px 16px;
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
            width: 100%;
        }

        .weather-card {
            width: 100%;
            max-width: 980px;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            text-align: center;
            transition: all 0.3s ease;
        }

        .weather-main {
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .weather-icon {
            font-size: clamp(88px, 10vw, 150px);
            line-height: 1;
            margin-bottom: 8px;
            filter: drop-shadow(0 0 18px rgba(255,255,255,0.2));
            position: relative;
            z-index: 3;
            width: 160px;
            height: 160px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .weather-icon .icon-primary {
            position: relative;
            z-index: 4;
        }

        .weather-icon .sun {
            position: relative;
            z-index: 4;
        }

        .weather-icon .cloud {
            position: absolute;
            z-index: 5;
            opacity: 0;
            pointer-events: none;
        }

        .weather-icon.cloudy .cloud {
            opacity: 1;
            animation: cloudDrift 10s ease-in-out infinite;
        }

        .sparkle-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 3;
        }

        .sparkle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            opacity: 0;
            animation: sparkle 2.5s infinite;
        }

        .lightning-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 6;
        }

        .lightning {
            position: absolute;
            font-size: 20px;
            opacity: 0;
            animation: lightningPop 0.4s ease-out forwards;
        }

        .weather-animation-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            z-index: 2;
        }

        .rain-layer {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .rain-layer.active {
            opacity: 0.3;
        }

        .rain-drop {
            position: absolute;
            top: 0;
            width: 2px;
            height: 12px;
            background: rgba(173,216,230,0.7);
            border-radius: 999px;
            animation: rainFall linear infinite;
        }

        .weather-temp-main,
        .weather-temp {
            font-size: clamp(120px, 20vw, 260px);
            font-weight: 900;
            text-align: center;
            line-height: 1;
            letter-spacing: -0.02em;
            text-shadow:
                0 10px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 3;
        }

        .weather-description {
            font-size: clamp(32px, 4vw, 44px);
            text-align: center;
            font-weight: 600;
            margin-top: 12px;
            letter-spacing: 0.08em;
            transition: all 0.3s ease;
            position: relative;
            z-index: 3;
            color: #facc15;
        }

        .weather-good {
            color: #facc15;
            text-shadow: 0 0 20px rgba(250, 204, 21, 0.3);
        }

        .weather-warning {
            color: #facc15;
            text-shadow: 0 0 20px rgba(250, 204, 21, 0.4);
        }

        .weather-bad {
            color: #f87171;
            text-shadow: 0 0 20px rgba(248, 113, 113, 0.4);
        }

        .weather-alert {
            margin-top: 20px;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            padding: 12px 24px;
            border-radius: 14px;
            backdrop-filter: blur(6px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
            position: relative;
            z-index: 3;
        }

        .weather-alert.rain {
            background: rgba(30, 58, 138, 0.85);
            color: #fff;
        }

        .weather-alert.storm {
            background: rgba(127, 29, 29, 0.9);
            color: #fff;
        }

        .break-container {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
            width: 100%;
            flex-wrap: wrap;
        }

        .break-card {
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(8px);
            border-radius: 18px;
            padding: 20px 30px;
            text-align: center;
            min-width: 180px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
        }

        .break-card .label {
            font-size: 1.2rem;
            opacity: 0.7;
            margin-bottom: 10px;
        }

        .break-card.rain {
            background: rgba(30, 58, 138, 0.55);
        }

        .break-card.storm {
            background: rgba(127, 29, 29, 0.6);
        }

        .break-card.dry {
            background: rgba(15, 23, 42, 0.5);
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

        .hidden { display: none !important; }

        @keyframes bgDrift {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.015); }
        }

        @keyframes weatherFade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes sunPulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 0.9; }
        }

        @keyframes cloudDrift {
            0% { transform: translateX(-12px); }
            50% { transform: translateX(12px); }
            100% { transform: translateX(-12px); }
        }

        @keyframes sparkle {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1); opacity: 1; }
            100% { transform: scale(0); opacity: 0; }
        }

        @keyframes rainFall {
            0% {
                transform: translateY(-20px);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0;
            }
        }

        @keyframes lightningPop {
            0% { opacity: 0; transform: scale(0.7); }
            40% { opacity: 0.45; transform: scale(1); }
            100% { opacity: 0; transform: scale(1.15); }
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
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow:
                0 14px 38px rgba(0,0,0,0.68),
                0 0 34px var(--house-color, rgba(255,255,255,0.22));
            background-color: var(--house-color, #334155) !important;
            position: relative;
            overflow: hidden;
        }

        .this-term-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.08), rgba(0,0,0,0.25));
            pointer-events: none;
        }

        .this-term-card > * {
            position: relative;
            z-index: 1;
        }

        .this-term-card.winner {
            transform: scale(1.03);
            animation: houseLeaderPulse 4.8s ease-in-out infinite;
            box-shadow:
                0 16px 46px rgba(0,0,0,0.75),
                0 0 56px var(--house-color, rgba(255,255,255,0.34));
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
            padding: 18px 28px;
            background: transparent !important;
            color: #ffffff;
        }

        .screen-title {
            font-size: clamp(42px, 4.8vw, 64px);
            font-weight: 900;
            margin: 0;
            margin-top: 4px;
            text-align: center;
            letter-spacing: 0.04em;
            text-shadow: 0 8px 28px rgba(0, 0, 0, 0.6);
        }

        .student-grid,
        .leaderboard-list,
        .top-list,
        .activity-list,
        .streak-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            grid-auto-rows: 1fr;
            gap: 12px 14px;
            width: 100%;
            max-width: 1240px;
            margin-left: auto;
            margin-right: auto;
            flex: 1;
            align-content: center;
            min-height: 0;
        }

        .leaderboard-list {
            margin-top: 14px;
            padding: 0 40px;
            position: relative;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-auto-flow: column;
            grid-template-rows: repeat(6, 1fr);
            gap: 24px;
        }

        .leaderboard-list::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 1px;
            background: rgba(255,255,255,0.08);
        }

        .leaderboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 0 60px;
            width: 100%;
            max-width: 1240px;
            margin: 14px auto 0;
            flex: 1;
            min-height: 0;
        }

        .leaderboard-column {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 0;
        }

        .student-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-radius: 18px;
            background-color: var(--house-color, #334155);
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 1.55rem;
            font-weight: 700;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 8px 25px rgba(0,0,0,0.7),
                0 0 20px var(--house-color);
            backdrop-filter: blur(6px);
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.45);
            height: 100%;
            animation: studentPulse 5.2s ease-in-out infinite;
        }

        .student-card::before {
            content: "";
            position: absolute;
            left: -50px;
            top: 0;
            width: 140px;
            height: 100%;
            background: radial-gradient(circle, var(--house-color), transparent 70%);
            opacity: 0.25;
        }

        .student-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.08), rgba(0,0,0,0.25));
            pointer-events: none;
        }

        .student-card > * {
            position: relative;
            z-index: 1;
        }

        .student-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .student-emoji {
            font-size: 1.35rem;
            filter: drop-shadow(0 0 8px rgba(0, 0, 0, 0.45));
        }

        .student-name {
            font-size: 1.55rem;
            letter-spacing: 0.3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .student-points {
            display: block;
            font-size: 1.08rem;
            font-weight: 700;
            opacity: 0.82;
            margin-top: 2px;
        }

        .student-rank {
            font-size: 1.1rem;
            opacity: 0.82;
            padding: 4px 10px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(2, 6, 23, 0.45);
            font-weight: 800;
            white-space: nowrap;
        }

        .student-card.is-top-1 {
            transform: scale(1.03);
            box-shadow:
                0 0 52px var(--house-color),
                0 14px 42px rgba(0,0,0,0.85);
            animation: studentPulseTop1 5s ease-in-out infinite;
        }

        .student-card.is-top-1 .student-rank::before {
            content: "👑 ";
        }

        .student-card.is-top-2,
        .student-card.is-top-3 {
            box-shadow:
                0 0 32px var(--house-color),
                0 11px 32px rgba(0,0,0,0.7),
                inset 0 1px 0 rgba(255,255,255,0.05);
        }

        .student-card:not(.is-top-1):not(.is-top-2):not(.is-top-3) {
            box-shadow:
                0 8px 25px rgba(0,0,0,0.7),
                0 0 12px var(--house-color);
        }

        .student-card.is-compact {
            padding: 12px 14px;
            border-radius: 16px;
        }

        .student-card.is-compact .student-name {
            font-size: 1.25rem;
        }

        .student-card.is-compact .student-emoji {
            font-size: 1.2rem;
        }

        .student-card.is-compact .student-rank {
            font-size: 0.95rem;
        }

        .student-card[data-house="Hufflepuff"] {
            color: #111;
            text-shadow: none;
        }

        .student-card[data-house="Hufflepuff"] .student-rank {
            border-color: rgba(0,0,0,0.25);
            background: rgba(255,255,255,0.35);
            color: #111;
        }

        .banner-container {
            display: flex;
            height: 100vh;
            gap: 12px;
            padding: 12px;
        }

        .banner {
            --house-color: #475569;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .banner.gryffindor { --house-color: #740001; }
        .banner.slytherin { --house-color: #1a472a; }
        .banner.ravenclaw { --house-color: #0e1a40; }
        .banner.hufflepuff { --house-color: #ffcc00; }

        .banner-inner {
            width: 85%;
            height: 90%;
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 20px;
            color: #fff;
            background: linear-gradient(
                180deg,
                var(--house-color),
                #000
            );
            box-shadow:
                0 20px 60px rgba(0,0,0,0.8),
                0 0 60px var(--house-color);
            position: relative;
            overflow: hidden;
        }

        .banner-inner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,0.08), rgba(0,0,0,0.35));
        }

        .banner-inner::after {
            content: "";
            position: absolute;
            width: 64%;
            height: 64%;
            border-radius: 999px;
            background: radial-gradient(circle, var(--house-color), transparent 68%);
            opacity: 0.22;
            filter: blur(8px);
            pointer-events: none;
        }

        .banner-inner > * {
            position: relative;
            z-index: 1;
        }

        .banner.hufflepuff .banner-inner {
            color: #111;
        }

        .banner-emoji {
            font-size: clamp(7rem, 12vw, 11rem);
            line-height: 1;
            filter: drop-shadow(0 0 24px rgba(255,255,255,0.2));
            animation: bannerPulse 4.6s ease-in-out infinite;
        }

        .banner-points {
            font-size: clamp(4rem, 7vw, 6rem);
            font-weight: 900;
        }

        @keyframes studentPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }

        @keyframes studentPulseTop1 {
            0%, 100% { transform: scale(1.03); }
            50% { transform: scale(1.06); }
        }

        @keyframes bannerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        @keyframes houseLeaderPulse {
            0%, 100% { transform: scale(1.03); }
            50% { transform: scale(1.06); }
        }

        .screen-inner.gryffindor,
        .screen-inner.slytherin,
        .screen-inner.ravenclaw,
        .screen-inner.hufflepuff { background: transparent; color: #fff; }
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
                    'Ravenclaw' => ['color' => '#0e1a40', 'emoji' => '🦅'],
                    'Hufflepuff' => ['color' => '#ffcc00', 'emoji' => '🦡'],
                    default => ['color' => '#444', 'emoji' => '🏫'],
                };
            }
        }
    @endphp

    <div id="emergencyScreen" style="display:none;">
        <div id="emergencyText"></div>
        <div class="em-sub">Follow instructions from staff</div>
    </div>

    <div id="broadcastBanner" class="tv-broadcast-banner" role="status" aria-live="polite"></div>

    <section class="tv-screen" id="screen-house-banners">
        <div class="banner-container">
            <div class="banner gryffindor">
                <div class="banner-inner">
                    <div class="banner-emoji">🦁</div>
                    <div class="banner-points">{{ $gryffindorPoints }}</div>
                </div>
            </div>
            <div class="banner slytherin">
                <div class="banner-inner">
                    <div class="banner-emoji">🐍</div>
                    <div class="banner-points">{{ $slytherinPoints }}</div>
                </div>
            </div>
            <div class="banner ravenclaw">
                <div class="banner-inner">
                    <div class="banner-emoji">🦅</div>
                    <div class="banner-points">{{ $ravenclawPoints }}</div>
                </div>
            </div>
            <div class="banner hufflepuff">
                <div class="banner-inner">
                    <div class="banner-emoji">🦡</div>
                    <div class="banner-points">{{ $hufflepuffPoints }}</div>
                </div>
            </div>
        </div>
    </section>

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
                        ['student_name' => 'JOSH', 'house_name' => 'Gryffindor', 'days' => 10],
                        ['student_name' => 'EMMA', 'house_name' => 'Slytherin', 'days' => 9],
                        ['student_name' => 'LIAM', 'house_name' => 'Ravenclaw', 'days' => 8],
                        ['student_name' => 'AVA', 'house_name' => 'Hufflepuff', 'days' => 7],
                        ['student_name' => 'NOAH', 'house_name' => 'Gryffindor', 'days' => 6],
                        ['student_name' => 'MIA', 'house_name' => 'Ravenclaw', 'days' => 5],
                        ['student_name' => 'ETHAN', 'house_name' => 'Slytherin', 'days' => 4],
                        ['student_name' => 'ISLA', 'house_name' => 'Hufflepuff', 'days' => 3],
                        ['student_name' => 'LUCAS', 'house_name' => 'Gryffindor', 'days' => 2],
                        ['student_name' => 'ARIA', 'house_name' => 'Ravenclaw', 'days' => 2],
                    ])->take(10);
                @endphp
                @foreach($streakData as $streak)
                    @php
                        $meta = houseMeta($streak['house_name']);
                        $rankClass = $loop->iteration === 1 ? ' is-top-1' : ($loop->iteration <= 3 ? ' is-top-2' : '');
                    @endphp
                    <div class="student-card{{ $rankClass }}" data-house="{{ $streak['house_name'] }}" style="--house-color: {{ $meta['color'] }}">
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
                        ['student_name' => 'JOSH', 'house_name' => 'Gryffindor', 'points' => 5, 'action' => 'Award', 'teacher' => 'Ms Blake'],
                        ['student_name' => 'EMMA', 'house_name' => 'Slytherin', 'points' => 4, 'action' => 'Commendation', 'teacher' => 'Mr Lee'],
                        ['student_name' => 'LIAM', 'house_name' => 'Ravenclaw', 'points' => 3, 'action' => 'Award', 'teacher' => 'Ms Stone'],
                        ['student_name' => 'AVA', 'house_name' => 'Hufflepuff', 'points' => 2, 'action' => 'Point', 'teacher' => 'Mr Cole'],
                        ['student_name' => 'NOAH', 'house_name' => 'Gryffindor', 'points' => 4, 'action' => 'Award', 'teacher' => 'Ms Blake'],
                        ['student_name' => 'MIA', 'house_name' => 'Ravenclaw', 'points' => 1, 'action' => 'Point', 'teacher' => 'Mr Wren'],
                        ['student_name' => 'ETHAN', 'house_name' => 'Slytherin', 'points' => 5, 'action' => 'Commendation', 'teacher' => 'Mr Lee'],
                        ['student_name' => 'ISLA', 'house_name' => 'Hufflepuff', 'points' => 2, 'action' => 'Point', 'teacher' => 'Ms Frost'],
                        ['student_name' => 'LUCAS', 'house_name' => 'Gryffindor', 'points' => 3, 'action' => 'Award', 'teacher' => 'Mr Cole'],
                        ['student_name' => 'ARIA', 'house_name' => 'Ravenclaw', 'points' => 1, 'action' => 'Point', 'teacher' => 'Ms Stone'],
                    ])->take(10);
                @endphp
                @foreach($activityData as $activity)
                    @php
                        $meta = houseMeta($activity['house_name']);
                    @endphp
                    <div class="student-card is-compact" data-house="{{ $activity['house_name'] }}" style="--house-color: {{ $meta['color'] }}">
                        <div class="student-left">
                            <span class="student-emoji">{{ $meta['emoji'] }}</span>
                            <span>
                                <span class="student-name">{{ $activity['student_name'] }}</span>
                                <span class="student-points">{{ $activity['action'] }} - {{ $activity['teacher'] }}</span>
                            </span>
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
                $left = $topStudents->slice(0, 5)->values();
                $right = $topStudents->slice(5, 5)->values();
            @endphp

            <div class="top-title">
                🏆 TOP STUDENTS
            </div>

            <div class="leaderboard-grid">
                <div class="leaderboard-column">
                    @foreach($left as $index => $student)
                        @php
                            $houseStyles = [
                                'Gryffindor' => ['color' => '#740001', 'emoji' => '🦁'],
                                'GryffINDOR' => ['color' => '#740001', 'emoji' => '🦁'],
                                'Slytherin' => ['color' => '#1a472a', 'emoji' => '🐍'],
                                'Ravenclaw' => ['color' => '#0e1a40', 'emoji' => '🦅'],
                                'Hufflepuff' => ['color' => '#ffcc00', 'emoji' => '🦡'],
                            ];
                            $house = $student->house_name ?? null;
                            $style = $houseStyles[$house] ?? ['color' => '#444', 'emoji' => '🏫'];
                            $rankNumber = $index + 1;
                            $rankClass = $rankNumber === 1 ? ' is-top-1' : ($rankNumber <= 3 ? ' is-top-2' : '');
                            $textColor = $house === 'Hufflepuff' ? '#111' : '#fff';
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="{{ $house }}" style="--house-color: {{ $style['color'] }}; color: {{ $textColor }};">
                            <div class="student-left">
                                <span class="student-emoji">{{ $style['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $rankNumber }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="leaderboard-column">
                    @foreach($right as $index => $student)
                        @php
                            $houseStyles = [
                                'Gryffindor' => ['color' => '#740001', 'emoji' => '🦁'],
                                'GryffINDOR' => ['color' => '#740001', 'emoji' => '🦁'],
                                'Slytherin' => ['color' => '#1a472a', 'emoji' => '🐍'],
                                'Ravenclaw' => ['color' => '#0e1a40', 'emoji' => '🦅'],
                                'Hufflepuff' => ['color' => '#ffcc00', 'emoji' => '🦡'],
                            ];
                            $house = $student->house_name ?? null;
                            $style = $houseStyles[$house] ?? ['color' => '#444', 'emoji' => '🏫'];
                            $rankNumber = $index + 6;
                            $rankClass = $rankNumber <= 3 ? ' is-top-2' : '';
                            $textColor = $house === 'Hufflepuff' ? '#111' : '#fff';
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="{{ $house }}" style="--house-color: {{ $style['color'] }}; color: {{ $textColor }};">
                            <div class="student-left">
                                <span class="student-emoji">{{ $style['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $rankNumber }}</div>
                        </div>
                    @endforeach
                </div>
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
                    @php
                        $houseMeta = houseMeta($entry['house'] ?? null);
                    @endphp
                    <div
                        class="house-card this-term-card{{ $loop->iteration === 1 ? ' winner' : '' }}"
                        style="--house-color: {{ $houseMeta['color'] }};"
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
                    @php
                        $houseMeta = houseMeta($entry['house'] ?? null);
                    @endphp
                    <div
                        class="house-card this-term-card{{ $loop->iteration === 1 ? ' winner' : '' }}"
                        style="--house-color: {{ $houseMeta['color'] }};"
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
                <div class="weather-card">
                    <div class="weather-main">
                        <div class="weather-animation-layer">
                            <div id="rain-layer" class="rain-layer"></div>
                        </div>
                        <div id="weather-icon" class="weather-icon">
                            <span id="weather-icon-primary" class="icon-primary">☀️</span>
                            <span id="weather-icon-sun" class="sun">☀️</span>
                            <span id="weather-icon-cloud" class="cloud">☁️</span>
                            <div id="sparkle-layer" class="sparkle-layer"></div>
                            <div id="lightning-layer" class="lightning-layer"></div>
                        </div>

                        <div id="weather-temp" class="weather-temp-main weather-temp">
                            {{ $weather[1]['temp'] }}°
                        </div>

                        <div id="weather-description" class="weather-description {{ $severityClass }}">
                            {{ $desc }}
                        </div>

                        <div id="weather-alert" class="weather-alert hidden"></div>
                    </div>

                    <div class="break-container">

                        @php
                            $recessRain = (collect($weather)->firstWhere('label', 'RECESS') ?? [])['rain'] ?? 0;
                            $lunchRain = (collect($weather)->firstWhere('label', 'LUNCH') ?? [])['rain'] ?? 0;
                        @endphp

                        <div class="break-card {{ $recessRain >= 40 ? 'rain' : 'dry' }}" id="recess-card">
                            <div class="label">RECESS</div>
                            <div id="weather-recess" class="{{ $recessRain >= 40 ? 'rain' : 'dry' }}">
                                {{ $recessRain >= 40 ? '🌧' : '☀️' }}
                            </div>
                        </div>

                        <div class="break-card {{ $lunchRain >= 40 ? 'rain' : 'dry' }}" id="lunch-card">
                            <div class="label">LUNCH</div>
                            <div id="weather-lunch" class="{{ $lunchRain >= 40 ? 'rain' : 'dry' }}">
                                {{ $lunchRain >= 40 ? '🌧' : '☀️' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="tv-screen" id="screen-top-gryffindor">
        <div class="screen-inner gryffindor">
            <h1 class="screen-title">Top 10 - Gryffindor</h1>
            @php
                $left = $topGryffindor->slice(0, 5)->values();
                $right = $topGryffindor->slice(5, 5)->values();
            @endphp
            <div class="leaderboard-grid">
                <div class="leaderboard-column">
                    @foreach($left as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Gryffindor');
                            $rankClass = $loop->iteration === 1 ? ' is-top-1' : ($loop->iteration <= 3 ? ' is-top-2' : '');
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="Gryffindor" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="leaderboard-column">
                    @foreach($right as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Gryffindor');
                        @endphp
                        <div class="student-card" data-house="Gryffindor" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 6 }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-slytherin">
        <div class="screen-inner slytherin">
            <h1 class="screen-title">Top 10 - Slytherin</h1>
            @php
                $left = $topSlytherin->slice(0, 5)->values();
                $right = $topSlytherin->slice(5, 5)->values();
            @endphp
            <div class="leaderboard-grid">
                <div class="leaderboard-column">
                    @foreach($left as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Slytherin');
                            $rankClass = $loop->iteration === 1 ? ' is-top-1' : ($loop->iteration <= 3 ? ' is-top-2' : '');
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="Slytherin" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="leaderboard-column">
                    @foreach($right as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Slytherin');
                        @endphp
                        <div class="student-card" data-house="Slytherin" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 6 }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-ravenclaw">
        <div class="screen-inner ravenclaw">
            <h1 class="screen-title">Top 10 - Ravenclaw</h1>
            @php
                $left = $topRavenclaw->slice(0, 5)->values();
                $right = $topRavenclaw->slice(5, 5)->values();
            @endphp
            <div class="leaderboard-grid">
                <div class="leaderboard-column">
                    @foreach($left as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Ravenclaw');
                            $rankClass = $loop->iteration === 1 ? ' is-top-1' : ($loop->iteration <= 3 ? ' is-top-2' : '');
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="Ravenclaw" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="leaderboard-column">
                    @foreach($right as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Ravenclaw');
                        @endphp
                        <div class="student-card" data-house="Ravenclaw" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 6 }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="tv-screen" id="screen-top-hufflepuff">
        <div class="screen-inner hufflepuff">
            <h1 class="screen-title">Top 10 - Hufflepuff</h1>
            @php
                $left = $topHufflepuff->slice(0, 5)->values();
                $right = $topHufflepuff->slice(5, 5)->values();
            @endphp
            <div class="leaderboard-grid">
                <div class="leaderboard-column">
                    @foreach($left as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Hufflepuff');
                            $rankClass = $loop->iteration === 1 ? ' is-top-1' : ($loop->iteration <= 3 ? ' is-top-2' : '');
                        @endphp
                        <div class="student-card{{ $rankClass }}" data-house="Hufflepuff" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 1 }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="leaderboard-column">
                    @foreach($right as $index => $student)
                        @php
                            $meta = houseMeta($student->house_name ?? 'Hufflepuff');
                        @endphp
                        <div class="student-card" data-house="Hufflepuff" style="--house-color: {{ $meta['color'] }}">
                            <div class="student-left">
                                <span class="student-emoji">{{ $meta['emoji'] }}</span>
                                <span>
                                    <span class="student-name">{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="student-points">{{ (int) $student->house_points }} pts</span>
                                </span>
                            </div>
                            <div class="student-rank">#{{ $index + 6 }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <button type="button" id="nextBtn" class="next-btn">Next ▶</button>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentScreen = 0;
    let ommExpiryTimeout = null;
    let emergencyActive = false;

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
        if (emergencyActive) {
            screens.forEach(function (s) { s.style.display = 'none'; });
            return;
        }
        const nextScreenId = screens[index] ? screens[index].id : '';
        const container = document.querySelector('.tv-container');
        if (nextScreenId !== 'screen-weather') {
            const weatherScreen = document.getElementById('screen-weather');
            if (weatherScreen) {
                weatherScreen.style.background = '';
            }
        }
        screens.forEach((s, i) => {
            s.style.display = (i === index) ? 'flex' : 'none';
        });
    }

    function nextScreen() {
        if (emergencyActive) return;
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

    const weatherBackgrounds = {
        clear: ['clear1.jpg', 'clear2.jpg', 'clear3.jpg'],
        cloud: ['cloud1.jpg', 'cloud2.jpg', 'cloud3.jpg'],
        rain: ['rain1.jpg', 'rain2.jpg', 'rain3.jpg'],
        storm: ['storm1.jpg', 'storm2.jpg']
    };
    let lastBg = null;
    let activeWeatherState = 'clear';

    function getRandomBackground(type) {
        const pool = weatherBackgrounds[type] || weatherBackgrounds.clear;
        let selected;
        do {
            selected = pool[Math.floor(Math.random() * pool.length)];
        } while (selected === lastBg && pool.length > 1);
        lastBg = selected;
        return "/weather/" + type + "/" + selected;
    }

    function setWeatherBackground(type) {
        const el = document.getElementById('screen-weather');
        const activeScreen = document.querySelector('.tv-screen:not([style*="display: none"])');

        if (!el) return;

        if (activeScreen && activeScreen.id === 'screen-weather') {
            const bg = getRandomBackground(type);

            el.style.background = `
            linear-gradient(rgba(0,0,0,0.25), rgba(0,0,0,0.35)),
            url('${bg}') center/cover no-repeat
        `;
        }
    }

    function updateWeatherBackground() {
        const weatherDescEl = document.getElementById('weather-description');
        const weatherTempEl = document.getElementById('weather-temp');
        const weatherIconEl = document.getElementById('weather-icon');
        const weatherIconPrimaryEl = document.getElementById('weather-icon-primary');
        const weatherIconSunEl = document.getElementById('weather-icon-sun');
        const weatherIconCloudEl = document.getElementById('weather-icon-cloud');
        const sparkleLayerEl = document.getElementById('sparkle-layer');
        const lightningLayerEl = document.getElementById('lightning-layer');
        const rainLayerEl = document.getElementById('rain-layer');
        const alertEl = document.getElementById('weather-alert');
        const recessEl = document.getElementById('weather-recess');
        const lunchEl = document.getElementById('weather-lunch');
        const recessCardEl = document.getElementById('recess-card');
        const lunchCardEl = document.getElementById('lunch-card');

        function updateBreakIcons(type) {
            if (!recessEl || !lunchEl) return;

            recessEl.classList.remove('rain', 'dry');
            lunchEl.classList.remove('rain', 'dry');
            if (recessCardEl) recessCardEl.classList.remove('rain', 'storm', 'dry');
            if (lunchCardEl) lunchCardEl.classList.remove('rain', 'storm', 'dry');

            if (type === 'rain' || type === 'storm') {
                recessEl.innerText = '🌧️';
                lunchEl.innerText = '🌧️';
                recessEl.classList.add('rain');
                lunchEl.classList.add('rain');
                if (recessCardEl) recessCardEl.classList.add(type);
                if (lunchCardEl) lunchCardEl.classList.add(type);
            } else if (type === 'cloud') {
                recessEl.innerText = '⛅';
                lunchEl.innerText = '⛅';
                recessEl.classList.add('dry');
                lunchEl.classList.add('dry');
                if (recessCardEl) recessCardEl.classList.add('dry');
                if (lunchCardEl) lunchCardEl.classList.add('dry');
            } else {
                recessEl.innerText = '☀️';
                lunchEl.innerText = '☀️';
                recessEl.classList.add('dry');
                lunchEl.classList.add('dry');
                if (recessCardEl) recessCardEl.classList.add('dry');
                if (lunchCardEl) lunchCardEl.classList.add('dry');
            }
        }

        function updateWeatherAlert(type) {
            if (!alertEl) return;

            alertEl.classList.remove('hidden', 'rain', 'storm');

            if (type === 'rain') {
                alertEl.innerText = 'WET WEATHER - INDOOR BREAK';
                alertEl.classList.add('rain');
            } else if (type === 'storm') {
                alertEl.innerText = 'SEVERE WEATHER - FOLLOW STAFF INSTRUCTIONS';
                alertEl.classList.add('storm');
            } else {
                alertEl.classList.add('hidden');
            }
        }

        function ensureRainDrops(count) {
            if (!rainLayerEl) return;
            if (rainLayerEl.dataset.count === String(count)) return;
            rainLayerEl.innerHTML = '';
            for (var i = 0; i < count; i++) {
                var drop = document.createElement('span');
                drop.className = 'rain-drop';
                drop.style.left = (Math.random() * 100).toFixed(2) + '%';
                drop.style.animationDuration = (1 + Math.random() * 1.1).toFixed(2) + 's';
                drop.style.animationDelay = (Math.random() * 2.5).toFixed(2) + 's';
                drop.style.opacity = (0.22 + Math.random() * 0.42).toFixed(2);
                rainLayerEl.appendChild(drop);
            }
            rainLayerEl.dataset.count = String(count);
        }

        function createSparkles() {
            if (!sparkleLayerEl) return;
            sparkleLayerEl.innerHTML = '';
            var count = 6 + Math.floor(Math.random() * 5);
            for (var i = 0; i < count; i++) {
                var s = document.createElement('span');
                s.className = 'sparkle';
                var angle = Math.random() * Math.PI * 2;
                var radius = 20 + Math.random() * 40;
                var x = 80 + Math.cos(angle) * radius;
                var y = 80 + Math.sin(angle) * radius;
                s.style.left = x.toFixed(1) + 'px';
                s.style.top = y.toFixed(1) + 'px';
                s.style.animationDelay = (Math.random() * 2).toFixed(2) + 's';
                sparkleLayerEl.appendChild(s);
            }
        }

        function clearAllStates() {
            activeWeatherState = 'clear';
            if (rainLayerEl) rainLayerEl.classList.remove('active');
            if (weatherIconEl) weatherIconEl.classList.remove('cloudy');
            if (sparkleLayerEl) sparkleLayerEl.innerHTML = '';
            if (lightningLayerEl) lightningLayerEl.innerHTML = '';
            if (weatherIconPrimaryEl) weatherIconPrimaryEl.style.display = 'inline';
            if (weatherIconSunEl) weatherIconSunEl.style.display = 'none';
            if (weatherIconCloudEl) weatherIconCloudEl.style.display = 'none';
        }

        fetch('https://api.open-meteo.com/v1/forecast?latitude=-42.88&longitude=147.33&current_weather=true')
            .then(res => res.json())
            .then(data => {
                const cw = data && data.current_weather ? data.current_weather : {};
                const code = Number(cw.weathercode);
                const temp = Number(cw.temperature);

                let state = 'clear';
                let text = 'Clear';
                let icon = '☀️';
                let severityClass = 'weather-good';

                if (code >= 1 && code <= 3) {
                    state = 'cloud';
                    text = 'Partly cloudy';
                    severityClass = 'weather-warning';
                } else if (code >= 45 && code <= 48) {
                    state = 'cloud';
                    text = 'Fog';
                    icon = '🌫️';
                    severityClass = 'weather-warning';
                } else if (code >= 51 && code <= 67) {
                    state = 'rain';
                    text = 'Rain';
                    icon = '🌧️';
                    severityClass = 'weather-bad';
                } else if (code >= 71 && code <= 77) {
                    state = 'cloud';
                    text = 'Snow';
                    icon = '❄️';
                    severityClass = 'weather-warning';
                } else if (code >= 80 && code <= 99) {
                    state = 'storm';
                    text = 'Heavy rain / storm';
                    icon = '⛈️';
                    severityClass = 'weather-bad';
                }

                clearAllStates();
                activeWeatherState = state;

                if (weatherTempEl && !isNaN(temp)) {
                    weatherTempEl.textContent = Math.round(temp) + '°';
                }
                if (weatherDescEl) {
                    weatherDescEl.textContent = text;
                    weatherDescEl.classList.remove('weather-good', 'weather-warning', 'weather-bad');
                    weatherDescEl.classList.add(severityClass);
                }

                if (state === 'clear') {
                    if (weatherIconPrimaryEl) {
                        weatherIconPrimaryEl.textContent = '☀️';
                        weatherIconPrimaryEl.style.display = 'inline';
                    }
                    createSparkles();
                } else if (state === 'cloud') {
                    if (weatherIconEl) weatherIconEl.classList.add('cloudy');
                    if (icon === '⛅') {
                        if (weatherIconPrimaryEl) weatherIconPrimaryEl.style.display = 'none';
                        if (weatherIconSunEl) weatherIconSunEl.style.display = 'inline';
                        if (weatherIconCloudEl) weatherIconCloudEl.style.display = 'inline';
                    } else {
                        if (weatherIconPrimaryEl) {
                            weatherIconPrimaryEl.textContent = icon;
                            weatherIconPrimaryEl.style.display = 'inline';
                        }
                    }
                } else if (state === 'rain') {
                    if (weatherIconPrimaryEl) {
                        weatherIconPrimaryEl.textContent = '🌧️';
                        weatherIconPrimaryEl.style.display = 'inline';
                    }
                    ensureRainDrops(48);
                    if (rainLayerEl) rainLayerEl.classList.add('active');
                } else if (state === 'storm') {
                    if (weatherIconPrimaryEl) {
                        weatherIconPrimaryEl.textContent = '⛈️';
                        weatherIconPrimaryEl.style.display = 'inline';
                    }
                    ensureRainDrops(52);
                    if (rainLayerEl) rainLayerEl.classList.add('active');
                }

                updateBreakIcons(state);
                updateWeatherAlert(state);

                setWeatherBackground(state);
            })
            .catch(function () {
                clearAllStates();
                createSparkles();
                updateBreakIcons('clear');
                updateWeatherAlert('clear');
                setWeatherBackground('clear');
            });
    }

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
                var emergencyColorMap = {
                    'Code Red': '#dc2626',
                    'Code Blue': '#2563eb',
                    'Code Yellow': '#eab308',
                    'Code Black': '#111827',
                    'Code Orange': '#f97316',
                    'Lockdown': '#7c3aed',
                    'Evacuation': '#16a34a'
                };

                if (message && message.startsWith('EMERGENCY:')) {
                    const code = message.slice('EMERGENCY:'.length).trim();
                    const emergencyBg = emergencyColorMap[code] || '#dc2626';
                    emergencyActive = true;
                    screens.forEach(function (s) { s.style.display = 'none'; });
                    if (emergencyScreen) {
                        emergencyScreen.style.display = 'flex';
                        emergencyScreen.style.background = emergencyBg;
                    }
                    if (emergencyText) {
                        emergencyText.innerText = code;
                    }
                    if (broadcastBanner) {
                        broadcastBanner.style.display = 'none';
                    }
                } else {
                    emergencyActive = false;
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
                emergencyActive = false;
                if (broadcastBanner) {
                    broadcastBanner.style.display = 'none';
                }
            });
    }

    setInterval(function () {
        if (activeWeatherState !== 'storm') return;
        if (Math.random() <= 0.75) return;
        const lightningLayer = document.getElementById('lightning-layer');
        if (!lightningLayer) return;
        const bolt = document.createElement('div');
        bolt.className = 'lightning';
        bolt.innerText = '⚡';
        bolt.style.left = (50 + Math.random() * 40 - 20) + 'px';
        bolt.style.top = (50 + Math.random() * 40 - 20) + 'px';
        lightningLayer.appendChild(bolt);
        setTimeout(function () { bolt.remove(); }, 400);
    }, 2000);

    fetchBroadcast();
    updateWeatherBackground();
    setInterval(fetchBroadcast, 5000);
    setInterval(updateWeatherBackground, 300000);
});
</script>

<script>
setInterval(function () {
    window.location.reload();
}, 300000);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const weatherScreen = document.getElementById('screen-weather');
    if (!weatherScreen || weatherScreen.style.display === 'none') return;

    const el = document.querySelector('.tv-container');
    if (!el) return;

    el.style.background = "url('/weather/rain/rain1.jpg') center/cover no-repeat";
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('.tv-container');
    if (!el) return;

    el.style.background = "url('/weather/rain/rain1.jpg') center/cover no-repeat";

    console.log('TEST BACKGROUND APPLIED');
});
</script>

</body>
</html>
