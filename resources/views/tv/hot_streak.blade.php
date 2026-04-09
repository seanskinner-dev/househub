<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hot Streak</title>

<style>
body {
    margin: 0;
    height: 100vh;
    background: radial-gradient(circle at center, #020617 0%, #000 100%);
    font-family: 'Segoe UI', sans-serif;
    color: white;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* MAIN */
.container {
    text-align: center;
    animation: fadeIn 1s ease;
}

/* TITLE */
.title {
    font-size: 5vh;
    margin-bottom: 2vh;
    letter-spacing: 0.4vh;
}

/* FIRE ICON */
.fire {
    font-size: 10vh;
    margin-bottom: 2vh;
    animation: flicker 1.5s infinite;
}

/* NAME */
.name {
    font-size: 11vh;
    font-weight: 900;
    margin-bottom: 2vh;

    text-shadow:
        0 0 30px rgba(255,120,0,0.8),
        0 0 60px rgba(255,80,0,0.4);
}

/* HOUSE */
.house {
    font-size: 4vh;
    margin-bottom: 3vh;
}

/* POINTS */
.points {
    font-size: 6vh;
    font-weight: bold;
    padding: 2vh 5vh;
    border-radius: 30px;
    display: inline-block;

    animation: pulse 2s infinite;
}

/* SUBTEXT */
.sub {
    margin-top: 2vh;
    font-size: 2vh;
    opacity: 0.6;
}

/* ANIMATIONS */
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.08); }
    100% { transform: scale(1); }
}

@keyframes flicker {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.15); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>

@if($streak)

<div class="container">

    <div class="title">🔥 HOT STREAK</div>

    <div class="fire">🔥</div>

    <div class="name">
        {{ $streak->first_name }} {{ $streak->last_name }}
    </div>

    <div class="house" style="color: {{ $streak->colour_hex }}">
        {{ $streak->house_name }}
    </div>

    <div class="points" style="background: {{ $streak->colour_hex }}">
        +{{ $streak->total }} points
    </div>

    <div class="sub">
        Last 60 minutes
    </div>

</div>

@else

<div class="container">
    <div class="title">NO CURRENT STREAK</div>
</div>

@endif

</body>
</html>