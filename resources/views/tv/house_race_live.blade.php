<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Race</title>

<style>
body {
    margin: 0;
    height: 100vh;
    background: radial-gradient(circle at center, #020617, #000);
    font-family: 'Segoe UI', sans-serif;
    color: white;
    overflow: hidden;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 6vh;
    margin: 2vh 0 3vh;
    font-weight: bold;
    letter-spacing: 0.3vh;
}

/* FULL HEIGHT CONTAINER */
.race {
    height: 80vh;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    padding: 0 6vw;
}

/* ROW */
.row {
    display: grid;
    grid-template-columns: 260px 1fr 120px;
    align-items: center;
    gap: 2vw;

    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.6s ease forwards;
}

.row:nth-child(1) { animation-delay: 0.1s; }
.row:nth-child(2) { animation-delay: 0.2s; }
.row:nth-child(3) { animation-delay: 0.3s; }
.row:nth-child(4) { animation-delay: 0.4s; }

/* HOUSE NAME */
.house {
    font-size: 3.2vh;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 1vw;
}

/* EMOJI */
.emoji {
    font-size: 3.5vh;
}

/* TRACK */
.track {
    width: 100%;
    height: 7vh;
    background: rgba(255,255,255,0.05);
    border-radius: 50px;
    overflow: hidden;
    position: relative;
}

/* BAR */
.bar {
    height: 100%;
    width: 0%;
    border-radius: 50px;
    transition: width 1.5s ease;

    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 2vw;

    font-weight: bold;
    font-size: 2.5vh;
}

/* SCORE */
.score {
    font-size: 3.5vh;
    text-align: right;
    font-weight: bold;
}

/* LEADER GLOW + PRESENCE */
.leader .bar {
    box-shadow:
        0 0 30px rgba(255,255,255,0.4),
        0 0 60px rgba(255,255,255,0.15);

    transform: scaleY(1.15);
}

/* SUBTLE TRACK GLOW */
.leader .track {
    background: rgba(255,255,255,0.1);
}

/* ANIMATION */
@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
</head>

<body>

<div class="title">🏁 House Race</div>

<div class="race">

@php
$houseEmojis = [
    'Gryffindor' => '🦁',
    'Slytherin' => '🐍',
    'Ravenclaw' => '🦅',
    'Hufflepuff' => '🦡',
];
@endphp

@foreach($houses as $house)
    <div class="row" data-points="{{ $house->points }}">
        
        <div class="house">
            <span class="emoji">
                {{ $houseEmojis[$house->name] ?? '🏠' }}
            </span>
            {{ $house->name }}
        </div>

        <div class="track">
            <div class="bar" style="background: {{ $house->colour_hex }}"></div>
        </div>

        <div class="score">
            {{ $house->points }}
        </div>

    </div>
@endforeach

</div>

<script>

const rows = document.querySelectorAll('.row');

// get max score
let max = 0;
rows.forEach(r => {
    const pts = parseInt(r.dataset.points);
    if (pts > max) max = pts;
});

// animate bars
rows.forEach(r => {
    const pts = parseInt(r.dataset.points);
    const percent = (pts / max) * 100;

    const bar = r.querySelector('.bar');

    setTimeout(() => {
        bar.style.width = percent + '%';
    }, 400);
});

// highlight leader
rows.forEach(r => {
    const pts = parseInt(r.dataset.points);
    if (pts === max) {
        r.classList.add('leader');
    }
});

</script>

</body>
</html>