<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Totals</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    color: white;
    background: radial-gradient(circle at center, #020617 0%, #000 100%);
    overflow: hidden;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 5vh;
    margin: 2vh 0;
    font-weight: bold;
    opacity: 0.9;
}

/* LAYOUT */
.container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    height: 85vh;
    gap: 2vh;
    padding: 0 3vw 3vh;
}

/* WINNER */
.winner {
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
    background: var(--color);

    animation: glow 2.5s ease-in-out infinite;
}

/* GLOW */
@keyframes glow {
    0% { box-shadow: 0 0 20px rgba(255,255,255,0.2); }
    50% { box-shadow: 0 0 70px rgba(255,255,255,0.6); }
    100% { box-shadow: 0 0 20px rgba(255,255,255,0.2); }
}

/* CROWN */
.crown {
    position: absolute;
    top: -20px;
    font-size: 6vh;
    animation: crownBounce 2s infinite;
}

@keyframes crownBounce {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* EMOJI */
.emoji {
    font-size: 5vh;
    margin-bottom: 1vh;
}

/* WINNER TEXT */
.winner .name {
    font-size: 6vh;
    font-weight: bold;
}

.winner .points {
    font-size: 10vh;
    margin-top: 1vh;
}

.ahead {
    margin-top: 2vh;
    font-size: 2.5vh;
    opacity: 0.9;
}

/* RIGHT COLUMN */
.side {
    display: grid;
    grid-template-rows: repeat(3, 1fr);
    gap: 2vh;
}

/* SMALL CARDS */
.card {
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: var(--color);
    position: relative;

    animation: float 6s ease-in-out infinite;
}

/* FLOAT */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0px); }
}

/* TEXT */
.name {
    font-size: 3vh;
}

.points {
    font-size: 5vh;
    font-weight: bold;
    margin-top: 1vh;
}

/* RANK */
.rank {
    position: absolute;
    top: 10px;
    left: 15px;
    font-size: 2vh;
    opacity: 0.7;
}
</style>
</head>

<body>

<div class="title">House Totals</div>

<div class="container">

    <!-- 🥇 WINNER -->
    @php
        $winner = $houses[0];
        $second = $houses[1] ?? null;
    @endphp

    <div class="winner" style="--color: {{ $winner->colour_hex }}">

        <div class="crown">👑</div>

        <div class="emoji">🥇</div>

        <div class="name">{{ $winner->name }}</div>

        <div class="points count" data-target="{{ $winner->points }}">0</div>

        @if($second)
        <div class="ahead">
            +{{ number_format($winner->points - $second->points) }} ahead
        </div>
        @endif

    </div>

    <!-- OTHER HOUSES -->
    <div class="side">

        @foreach($houses->slice(1) as $index => $house)
        <div class="card" style="--color: {{ $house->colour_hex }}">

            <div class="rank">#{{ $index + 2 }}</div>

            <div class="name">{{ $house->name }}</div>

            <div class="points count" data-target="{{ $house->points }}">0</div>

        </div>
        @endforeach

    </div>

</div>

<script>

// 🔢 COUNT-UP ANIMATION
document.querySelectorAll('.count').forEach(el => {

    let target = parseInt(el.dataset.target);
    let count = 0;
    let step = target / 60;

    let interval = setInterval(() => {
        count += step;

        if (count >= target) {
            count = target;
            clearInterval(interval);
        }

        el.innerText = Math.floor(count).toLocaleString();
    }, 25);

});

</script>

</body>
</html>