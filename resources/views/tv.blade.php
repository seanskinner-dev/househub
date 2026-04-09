<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Leaderboard</title>

<style>
body {
    margin: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;

    background: #000;
    font-family: Arial, sans-serif;
    color: white;
}

/* TITLE */
h1 {
    text-align: center;
    font-size: 5vh;
    margin: 2vh 0;
    font-weight: 700;
}

/* MAIN */
.container {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2vh;
    padding: 1vh 2vw;
}

/* 💥 BASE CARD */
.card {
    border-radius: 1.5vw;
    padding: 2vh 3vw;

    display: flex;
    align-items: center;
    justify-content: space-between;

    color: white;

    /* 🔥 STRONG EDGE */
    border: 4px solid white;
}

/* 🥇 TOP */
.top-house {
    flex: 2;
    padding: 3vh 4vw;
}

/* 🥈🥉 */
.second-row {
    flex: 1.5;
    display: flex;
    gap: 1.5vw;
}

.side-house {
    flex: 1;
}

/* REST */
.list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1vh;
}

.house {
    flex: 1;
}

/* 🟡 HOUSE COLOURS — FULL SOLID */
.gryffindor {
    background: #740001;
}

.slytherin {
    background: #1a472a;
}

.ravenclaw {
    background: #0e1a40;
}

.hufflepuff {
    background: #ffcc00;
    color: black;
}

/* TEXT */
.left {
    display: flex;
    align-items: center;
    gap: 2vw;
}

.emoji {
    font-size: 6vh;
}

.text {
    display: flex;
    flex-direction: column;
}

.rank {
    font-size: 2vh;
    opacity: 0.9;
}

.name {
    font-weight: bold;
}

/* SIZE */
.top-house .name { font-size: 6vh; }
.side-house .name { font-size: 4.5vh; }
.house .name { font-size: 4vh; }

/* POINTS */
.points {
    font-weight: bold;
}
.top-house .points { font-size: 7vh; }
.side-house .points { font-size: 5vh; }
.house .points { font-size: 4.5vh; }

/* 🏆 HERO */
.top-house {
    transform: scale(1.03);
}

/* OPTIONAL: slight hover glow (nice on dev screen) */
.card:hover {
    filter: brightness(1.1);
}

/* 🔥 NEW: DISCREET NEXT BUTTON */
.next-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;

    background: rgba(255,255,255,0.08);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);

    padding: 8px 12px;
    border-radius: 10px;

    font-size: 12px;
    cursor: pointer;

    opacity: 0.15;
    transition: 0.2s;
}

.next-btn:hover {
    opacity: 1;
    background: rgba(255,255,255,0.2);
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

function houseClass($name) {
    return strtolower($name);
}

$top = $houses[0] ?? null;
$second = $houses[1] ?? null;
$third = $houses[2] ?? null;
$rest = $houses->slice(3);
@endphp

<h1>House Leaderboard</h1>

<div class="container">

    @if($top)
    <div class="card top-house {{ houseClass($top->name) }}">
        <div class="left">
            <div class="emoji">{{ houseEmoji($top->name) }}</div>
            <div class="text">
                <div class="rank">#1</div>
                <div class="name">{{ $top->name }}</div>
            </div>
        </div>
        <div class="points">{{ $top->points }}</div>
    </div>
    @endif

    <div class="second-row">

        @if($second)
        <div class="card side-house {{ houseClass($second->name) }}">
            <div class="left">
                <div class="emoji">{{ houseEmoji($second->name) }}</div>
                <div class="text">
                    <div class="rank">#2</div>
                    <div class="name">{{ $second->name }}</div>
                </div>
            </div>
            <div class="points">{{ $second->points }}</div>
        </div>
        @endif

        @if($third)
        <div class="card side-house {{ houseClass($third->name) }}">
            <div class="left">
                <div class="emoji">{{ houseEmoji($third->name) }}</div>
                <div class="text">
                    <div class="rank">#3</div>
                    <div class="name">{{ $third->name }}</div>
                </div>
            </div>
            <div class="points">{{ $third->points }}</div>
        </div>
        @endif

    </div>

    <div class="list">
        @foreach($rest as $index => $house)
        <div class="card house {{ houseClass($house->name) }}">
            <div class="left">
                <div class="emoji">{{ houseEmoji($house->name) }}</div>
                <div class="text">
                    <div class="rank">#{{ $index + 4 }}</div>
                    <div class="name">{{ $house->name }}</div>
                </div>
            </div>
            <div class="points">{{ $house->points }}</div>
        </div>
        @endforeach
    </div>

</div>

<!-- 🔥 NEW BUTTON (ONLY ADDITION) -->
<button class="next-btn" onclick="nextScreen()">Next ▶</button>

<script>
window.setInterval = function() {};
window.setTimeout = function(fn, t) {
    return setTimeout(fn, t);
};

/* 🔥 NEW: TV NEXT HANDLER */
function nextScreen() {
    window.location.href = '/tv/house-trends';
}

/* 🔥 OPTIONAL: keyboard support */
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') nextScreen();
});
</script>

</body>
</html>