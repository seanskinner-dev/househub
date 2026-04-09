<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Today Leader</title>

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
    opacity: 0.85;
    margin-bottom: 2vh;
}

/* TROPHY */
.trophy {
    font-size: 10vh;
    margin-bottom: 2vh;
    animation: bounce 2s infinite;
}

/* NAME */
.name {
    font-size: 11vh;
    font-weight: 900;
    margin-bottom: 2vh;

    text-shadow:
        0 0 20px rgba(255,255,255,0.25),
        0 0 40px rgba(255,255,255,0.1);
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
        0 0 20px rgba(255,255,255,0.2);

    animation: pulse 2s infinite;
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

@keyframes bounce {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-14px); }
}
</style>

</head>
<body>

@if($winner)

<div class="container">

    <div class="title">🔥 TODAY’S LEADER</div>

    <div class="trophy">🏆</div>

    <div class="name">
        {{ $winner->first_name }} {{ $winner->last_name }}
    </div>

    <div class="house" style="color: {{ $winner->colour_hex }}">
        {{ $winner->house_name }}
    </div>

    <div class="points" style="background: {{ $winner->colour_hex }}">
        +{{ $winner->total }} points
    </div>

</div>

@else

<div class="container">
    <div class="title">NO ACTIVITY TODAY</div>
</div>

@endif

</body>
</html>