<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Live Activity</title>

<style>
body {
    margin: 0;
    height: 100vh;
    background: radial-gradient(circle at center, #020617 0%, #000 100%);
    font-family: 'Segoe UI', sans-serif;
    color: white;
    overflow: hidden;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 5vh;
    margin: 2vh 0;
    letter-spacing: 0.4vh;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2vh;
    padding: 2vh 4vw;
}

/* CARD */
.card {
    padding: 2vh;
    border-radius: 20px;
    background: rgba(255,255,255,0.05);

    display: flex;
    justify-content: space-between;
    align-items: center;

    transform: translateY(20px);
    opacity: 0;
    animation: fadeUp 0.6s ease forwards;
}

/* stagger animation */
.card:nth-child(1) { animation-delay: 0.05s; }
.card:nth-child(2) { animation-delay: 0.1s; }
.card:nth-child(3) { animation-delay: 0.15s; }
.card:nth-child(4) { animation-delay: 0.2s; }
.card:nth-child(5) { animation-delay: 0.25s; }
.card:nth-child(6) { animation-delay: 0.3s; }
.card:nth-child(7) { animation-delay: 0.35s; }
.card:nth-child(8) { animation-delay: 0.4s; }

/* LEFT */
.left {
    display: flex;
    flex-direction: column;
}

/* NAME */
.name {
    font-size: 3.2vh;
    font-weight: bold;
}

/* DETAILS */
.details {
    font-size: 2vh;
    opacity: 0.7;
}

/* POINTS */
.points {
    font-size: 4.5vh;
    font-weight: bold;
    animation: pulse 2s infinite;
}

/* POSITIVE GLOW */
.points.positive {
    text-shadow:
        0 0 10px rgba(0,255,120,0.8),
        0 0 20px rgba(0,255,120,0.4);
}

/* NEGATIVE GLOW */
.points.negative {
    text-shadow:
        0 0 10px rgba(255,60,60,0.8),
        0 0 20px rgba(255,60,60,0.4);
}

/* ANIMATIONS */
@keyframes fadeUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.08); }
    100% { transform: scale(1); }
}
</style>
</head>

<body>

<div class="title">⚡ LIVE ACTIVITY</div>

<div class="grid">

@foreach($activities as $activity)

<div class="card" style="border-left: 10px solid {{ $activity->colour_hex ?? '#666' }}">

    <div class="left">
        <div class="name">
            {{ $activity->first_name ?? '🏠 House Award' }} {{ $activity->last_name ?? '' }}
        </div>

        <div class="details">
            {{ $activity->house_name ?? '' }}
            @if($activity->teacher)
                • {{ $activity->teacher }}
            @endif
        </div>
    </div>

    <div class="points {{ $activity->amount >= 0 ? 'positive' : 'negative' }}"
         style="color: {{ $activity->colour_hex ?? '#fff' }}">

        {{ $activity->amount >= 0 ? '+' : '' }}{{ $activity->amount }}

    </div>

</div>

@endforeach

</div>

</body>
</html>