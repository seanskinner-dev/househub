<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Momentum</title>

<style>
body {
    margin: 0;
    height: 100vh;
    background: radial-gradient(circle at center, #01040a 0%, #000 100%);
    font-family: Arial, sans-serif;
    color: white;
    overflow: hidden;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 6vh;
    margin: 3vh 0;
    font-weight: bold;

    text-shadow: 0 0 20px rgba(255,255,255,0.2);
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4vh;
    padding: 4vh 6vw;
}

/* CARD */
.card {
    padding: 50px;
    border-radius: 24px;

    background: #020617;

    box-shadow:
        0 0 40px rgba(0,0,0,0.9),
        inset 0 0 20px rgba(255,255,255,0.02);

    text-align: center;
    position: relative;
}

/* LEADER CARD */
.card.leader {
    box-shadow:
        0 0 60px rgba(255,255,255,0.05),
        inset 0 0 30px rgba(255,255,255,0.03);
}

/* HOUSE NAME */
.name {
    font-size: 3vh;
    margin-bottom: 25px;
    opacity: 0.9;
}

/* VALUE */
.value {
    font-size: 10vh;
    font-weight: bold;
}

/* POSITIVE */
.positive {
    color: #22c55e;
    text-shadow:
        0 0 20px rgba(34,197,94,0.6),
        0 0 60px rgba(34,197,94,0.4);
}

/* NEGATIVE */
.negative {
    color: #ef4444;
    text-shadow:
        0 0 20px rgba(239,68,68,0.6),
        0 0 60px rgba(239,68,68,0.4);
}

/* ARROW */
.arrow {
    font-size: 4vh;
    margin-top: 10px;
    opacity: 0.8;
}

/* LEADER BADGE */
.badge {
    position: absolute;
    top: 20px;
    right: 20px;

    font-size: 1.8vh;
    padding: 6px 10px;
    border-radius: 6px;

    background: rgba(255,255,255,0.05);
    color: #fff;
}
</style>
</head>
<body>

<div class="title">Weekly Momentum</div>

<div class="grid">

@foreach($data as $index => $house)

<div class="card {{ $index === 0 ? 'leader' : '' }}">

    @if($index === 0)
        <div class="badge">Top Momentum</div>
    @endif

    <div class="name">
        {{ $house->name }}
    </div>

    <div class="value {{ $house->total >= 0 ? 'positive' : 'negative' }}" 
         data-value="{{ $house->total }}">

        0
    </div>

    <div class="arrow">
        {{ $house->total >= 0 ? '↑' : '↓' }}
    </div>

</div>

@endforeach

</div>

<script>
// 🔥 COUNT-UP ANIMATION
function animateValue(el, target) {
    let start = 0;
    const duration = 800;
    const step = target / (duration / 16);

    function update() {
        start += step;

        if ((target >= 0 && start >= target) || (target < 0 && start <= target)) {
            el.textContent = (target >= 0 ? '+' : '') + target;
            return;
        }

        el.textContent = (target >= 0 ? '+' : '') + Math.floor(start);
        requestAnimationFrame(update);
    }

    update();
}

document.querySelectorAll('.value').forEach(el => {
    const target = parseInt(el.dataset.value);
    animateValue(el, target);
});
</script>

</body>
</html>