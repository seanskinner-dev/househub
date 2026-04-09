<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Highlights</title>

<style>
body {
    margin: 0;
    background: radial-gradient(circle at center, #020617 0%, #000 100%);
    font-family: Arial, sans-serif;
    color: white;
    overflow: hidden;
}

h1 {
    position: absolute;
    top: 40px;
    width: 100%;
    text-align: center;
    font-size: 64px;
    letter-spacing: 2px;
    opacity: 0.9;
}

.stage {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    position: absolute;
    text-align: center;
    padding: 100px 120px;
    border-radius: 40px;

    background: linear-gradient(145deg, #1e293b, #020617);

    box-shadow:
        0 0 80px rgba(34,197,94,0.15),
        0 0 200px rgba(34,197,94,0.08);

    opacity: 0;
    transform: scale(0.85);
    transition: all 0.8s ease;
}

.card.active {
    opacity: 1;
    transform: scale(1);
}

.name {
    font-size: 72px;
    font-weight: bold;
    margin-bottom: 40px;
}

.points {
    font-size: 120px;
    font-weight: bold;
    color: #22c55e;

    text-shadow:
        0 0 20px rgba(34,197,94,0.6),
        0 0 60px rgba(34,197,94,0.4);
}

.label {
    margin-top: 30px;
    font-size: 28px;
    color: #94a3b8;
    line-height: 1.6;
}
</style>
</head>
<body>

<h1>Teacher Highlights This Week</h1>

<div class="stage">

    @foreach($teachers as $index => $teacher)
        <div class="card" id="card-{{ $index }}">
            <div class="name">{{ $teacher->name }}</div>

            <div class="points">+{{ $teacher->total_points }}</div>

            <div class="label">
                points awarded this week<br>
                Supporting {{ $teacher->students_supported ?? 0 }} students
            </div>
        </div>
    @endforeach

</div>

<script>
let current = 0;
const cards = document.querySelectorAll('.card');
let running = true;

// show card
function showCard(index) {
    cards.forEach(card => card.classList.remove('active'));
    cards[index].classList.add('active');
}

// safe rotation loop (NO setInterval)
function rotateLoop() {
    if (!running) return;

    showCard(current);
    current = (current + 1) % cards.length;

    setTimeout(rotateLoop, 6000);
}

// pause when tab not visible (IMPORTANT)
document.addEventListener('visibilitychange', () => {
    running = !document.hidden;

    if (running) {
        rotateLoop();
    }
});

// initial start
showCard(0);
setTimeout(rotateLoop, 6000);
</script>

</body>
</html>