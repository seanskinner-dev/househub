<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Weekly Leader</title>

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
    letter-spacing: 0.5vh;
    margin-bottom: 2vh;
    opacity: 0.9;
}

/* ICON */
.icon {
    font-size: 10vh;
    margin-bottom: 2vh;
    animation: float 3s ease-in-out infinite;
}

/* NAME */
.name {
    font-size: 11vh;
    font-weight: 900;
    margin-bottom: 2vh;

    text-shadow:
        0 0 25px rgba(255,255,255,0.25),
        0 0 50px rgba(255,255,255,0.1);
}

/* HOUSE */
.house {
    font-size: 4vh;
    margin-bottom: 4vh;
    font-weight: 600;
}

/* POINTS */
.points {
    font-size: 6vh;
    font-weight: bold;
    padding: 2vh 5vh;
    border-radius: 25px;
    display: inline-block;

    box-shadow:
        0 0 25px rgba(255,255,255,0.2);

    animation: pulse 2.2s infinite;
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

@keyframes float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}
</style>

</head>
<body>

@if($winner)

<div class="container">

    <div class="title">👑 WEEKLY LEADER</div>

    <div class="icon">🔥</div>

    <div class="name">
        {{ $winner->first_name }} {{ $winner->last_name }}
    </div>

    <div class="house" style="color: {{ $winner->colour_hex }}">
        {{ $winner->house_name }}
    </div>

    <div class="points" style="background: {{ $winner->colour_hex }}">
        +{{ $winner->total }} points
    </div>

    <div class="sub">
        This week so far
    </div>

</div>

@else

<div class="container">
    <div class="title">NO ACTIVITY THIS WEEK</div>
</div>

@endif

</body>
</html>