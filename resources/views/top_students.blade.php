<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Top Students</title>

<style>
body {
    margin: 0;
    background: radial-gradient(circle at center, #020617 0%, #000 100%);
    font-family: Arial, sans-serif;
    color: white;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 6vh;
    margin: 3vh 0;
    font-weight: bold;
    letter-spacing: 0.1vw;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2vh;
    padding: 0 4vw 4vh;
}

/* CARD */
.card {
    position: relative;
    background: linear-gradient(135deg, #0f172a, #020617);
    border-radius: 16px;
    padding: 2.2vh 2vw;

    display: flex;
    justify-content: space-between;
    align-items: center;

    box-shadow: 0 0 25px rgba(0,0,0,0.7);
}

/* LEFT COLOUR STRIP */
.strip {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 8px;
    border-radius: 16px 0 0 16px;
}

/* NAME */
.name {
    font-size: 2.8vh;
    font-weight: bold;
}

/* META */
.meta {
    font-size: 1.8vh;
    opacity: 0.7;
}

/* POINTS */
.points {
    font-size: 3.5vh;
    font-weight: bold;
}

/* RANK */
.rank {
    font-size: 1.6vh;
    opacity: 0.6;
    margin-bottom: 0.5vh;
}

/* TOP 3 PREMIUM */
.top1 {
    transform: scale(1.03);
    box-shadow: 0 0 40px rgba(255,215,0,0.9);
}
.top2 {
    transform: scale(1.02);
    box-shadow: 0 0 30px rgba(192,192,192,0.8);
}
.top3 {
    transform: scale(1.01);
    box-shadow: 0 0 25px rgba(205,127,50,0.8);
}
</style>
</head>

<body>

<div class="title">Top Students</div>

<div class="grid">

@foreach($students as $i => $student)

@php
    $emoji = match($student->house_name) {
        'Gryffindor' => '🦁',
        'Slytherin' => '🐍',
        'Ravenclaw' => '🦅',
        'Hufflepuff' => '🦡',
        default => '🎓'
    };
@endphp

<div class="card 
    @if($i==0) top1 
    @elseif($i==1) top2 
    @elseif($i==2) top3 
    @endif">

    <!-- House Colour Strip -->
    <div class="strip" style="background: {{ $student->colour_hex }}"></div>

    <div>
        <div class="rank">#{{ $i + 1 }}</div>

        <div class="name">
            {{ $emoji }} {{ $student->first_name }} {{ $student->last_name }}
        </div>

        <div class="meta">
            Y{{ $student->year_level }} • {{ $student->house_name }}
        </div>
    </div>

    <div class="points">
        {{ $student->points }}
    </div>

</div>

@endforeach

</div>

</body>
</html>