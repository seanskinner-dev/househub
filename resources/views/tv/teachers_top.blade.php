<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Top Teachers</title>

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
    margin: 3vh 0;
    letter-spacing: 0.5vh;
}

/* CONTAINER */
.container {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 3vw;
    height: 75vh;
}

/* CARD */
.card {
    width: 22vw;
    text-align: center;
    border-radius: 20px;
    padding: 2vh;
    background: rgba(255,255,255,0.05);

    display: flex;
    flex-direction: column;
    justify-content: flex-end;

    animation: fadeInUp 0.8s ease;
}

/* POSITIONS */
.first {
    height: 70%;
    transform: scale(1.1);
}

.second {
    height: 60%;
}

.third {
    height: 55%;
}

/* RANK */
.rank {
    font-size: 4vh;
    margin-bottom: 1vh;
}

/* NAME */
.name {
    font-size: 3.5vh;
    font-weight: bold;
    margin-bottom: 1vh;
}

/* ACTIONS */
.actions {
    font-size: 2.5vh;
    opacity: 0.8;
    margin-bottom: 1vh;
}

/* POINTS */
.points {
    font-size: 3vh;
    font-weight: bold;
}

/* ANIMATION */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>

<div class="title">👨‍🏫 TOP TEACHERS</div>

<div class="container">

@if(count($teachers) >= 2)
    <div class="card second">
        <div class="rank">🥈</div>
        <div class="name">{{ $teachers[1]->name }}</div>
        <div class="actions">{{ $teachers[1]->actions }} awards</div>
        <div class="points">{{ $teachers[1]->total_points }} pts</div>
    </div>
@endif

@if(count($teachers) >= 1)
    <div class="card first">
        <div class="rank">🥇</div>
        <div class="name">{{ $teachers[0]->name }}</div>
        <div class="actions">{{ $teachers[0]->actions }} awards</div>
        <div class="points">{{ $teachers[0]->total_points }} pts</div>
    </div>
@endif

@if(count($teachers) >= 3)
    <div class="card third">
        <div class="rank">🥉</div>
        <div class="name">{{ $teachers[2]->name }}</div>
        <div class="actions">{{ $teachers[2]->actions }} awards</div>
        <div class="points">{{ $teachers[2]->total_points }} pts</div>
    </div>
@endif

</div>

</body>
</html>