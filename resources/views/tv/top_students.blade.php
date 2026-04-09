<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Students</title>

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;

            background: radial-gradient(circle at center, #01040a 0%, #000 100%);
            font-family: Arial, sans-serif;
            color: white;
            overflow: hidden;
        }

        h1 {
            text-align: center;
            font-size: 4vh;
            margin: 1vh 0;
        }

        .top3 {
            display: flex;
            justify-content: center;
            gap: 2vw;
            padding: 1vh 0;
        }

        .hero {
            flex: 1;
            max-width: 18vw;
            padding: 1.5vh;
            border-radius: 1vw;
            text-align: center;

            background: linear-gradient(145deg, #0f172a, #020617);
            position: relative;

            box-shadow:
                0 0 1vw rgba(255,255,255,0.05),
                0 0 2vw rgba(255,255,255,0.05);
        }

        .hero::before {
            content: '';
            position: absolute;
            left: 0;
            width: 0.5vw;
            height: 100%;
            background: var(--house-color);
            box-shadow: 0 0 1.5vw var(--house-color);
        }

        .hero-rank {
            position: absolute;
            top: 0.6vh;
            left: 0.8vw;

            font-size: 1.6vh;
            font-weight: bold;

            background: rgba(0,0,0,0.6);
            padding: 0.3vh 0.6vw;
            border-radius: 0.4vw;
        }

        .hero .name {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6vw;
            padding-left: 2.5vw;
        }

        .emoji {
            font-size: 3vh;
            filter: drop-shadow(0 0 0.8vw var(--house-color));
        }

        .points {
            font-size: 2.2vh;
            color: #22c55e;
        }

        .grid {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: repeat(12, 1fr);
            gap: 0.5vh;
            padding: 1vh 2vw;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 0.8vh 1vw;
            border-radius: 0.6vw;

            background: linear-gradient(145deg, #0f172a, #020617);

            box-shadow:
                0 0 0.6vw rgba(255,255,255,0.05);

            position: relative;
            overflow: hidden;
        }

        .row::before {
            content: '';
            position: absolute;
            left: 0;
            width: 0.4vw;
            height: 100%;
            background: var(--house-color);
            box-shadow: 0 0 1vw var(--house-color);
        }

        .row::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255,255,255,0.08),
                transparent
            );
            animation: shimmer 5s infinite;
        }

        .name {
            display: flex;
            align-items: center;
            gap: 0.8vw;
            font-size: 1.5vh;
        }

        .rank {
            width: 2vw;
            opacity: 0.6;
        }

        .points-small {
            font-size: 1.5vh;
            color: #22c55e;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
    </style>
</head>
<body>

@php
function houseEmoji($house) {
    return match($house) {
        'Gryffindor' => '🦁',
        'Slytherin' => '🐍',
        'Ravenclaw' => '🦅',
        'Hufflepuff' => '🦡',
        default => '🏫',
    };
}

// 🔥 UPDATED COLOURS (TV OPTIMISED)
function houseColor($house) {
    return match($house) {
        'Gryffindor' => '#dc2626',
        'Slytherin' => '#16a34a',
        'Ravenclaw' => '#2563eb', // FIXED BLUE
        'Hufflepuff' => '#facc15',
        default => '#475569',
    };
}

$students = $students->take(27);
$rest = $students->slice(3)->values();

$left = $rest->slice(0, 12)->values();
$right = $rest->slice(12, 12)->values();
@endphp

<h1>Top Students</h1>

<div class="top3">
    @foreach($students->slice(0,3)->values() as $i => $student)
        <div class="hero" style="--house-color: {{ houseColor($student->house_name) }}">

            <div class="hero-rank">#{{ $i + 1 }}</div>

            <div class="name">
                <span class="emoji">{{ houseEmoji($student->house_name) }}</span>
                {{ $student->first_name }} {{ $student->last_name }}
            </div>

            <div class="points">{{ $student->house_points }}</div>
        </div>
    @endforeach
</div>

<div class="grid">

    @for ($i = 0; $i < 12; $i++)

        @if(isset($left[$i]))
            <div class="row" style="--house-color: {{ houseColor($left[$i]->house_name) }}">
                <div class="name">
                    <span class="rank">#{{ $i + 4 }}</span>
                    <span class="emoji">{{ houseEmoji($left[$i]->house_name) }}</span>
                    {{ $left[$i]->first_name }} {{ $left[$i]->last_name }}
                </div>
                <div class="points-small">{{ $left[$i]->house_points }}</div>
            </div>
        @endif

        @if(isset($right[$i]))
            <div class="row" style="--house-color: {{ houseColor($right[$i]->house_name) }}">
                <div class="name">
                    <span class="rank">#{{ $i + 4 + $left->count() }}</span>
                    <span class="emoji">{{ houseEmoji($right[$i]->house_name) }}</span>
                    {{ $right[$i]->first_name }} {{ $right[$i]->last_name }}
                </div>
                <div class="points-small">{{ $right[$i]->house_points }}</div>
            </div>
        @endif

    @endfor

</div>

</body>
</html>