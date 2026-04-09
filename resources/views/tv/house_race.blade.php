<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Race</title>

<style>
body {
    margin: 0;
    height: 100vh;
    background: radial-gradient(circle at center, #01040a 0%, #000 100%);
    font-family: Arial, sans-serif;
    color: white;
    overflow: hidden;
}

/* MAIN LAYOUT */
.wrapper {
    display: flex;
    height: 100vh;
    padding: 40px 60px;
    box-sizing: border-box;
    gap: 40px;
}

/* LEFT PANEL */
.left {
    flex: 7;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* RIGHT PANEL */
.right {
    flex: 3;
    background: rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 30px;
    display: flex;
    flex-direction: column;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 6vh;
    margin-bottom: 3vh;
    font-weight: bold;
    letter-spacing: 0.1vw;
    text-shadow: 0 0 20px rgba(255,255,255,0.2);
}

/* CONTAINER */
.race {
    display: flex;
    flex-direction: column;
    gap: 3vh;
}

/* ROW */
.row {
    display: grid;
    grid-template-columns: 250px 1fr 150px;
    align-items: center;
    gap: 2vw;
}

/* LABEL */
.label {
    font-size: 3.5vh;
    font-weight: bold;
}

/* SCORE */
.score {
    font-size: 5vh;
    font-weight: bold;
    text-align: right;
    text-shadow: 0 0 10px rgba(255,255,255,0.2);
}

/* BAR */
.bar {
    height: 5vh;
    background: rgba(255,255,255,0.05);
    border-radius: 999px;
    overflow: hidden;
    position: relative;
}

/* FILL */
.fill {
    height: 100%;
    width: 0;
    background: var(--color);
    box-shadow:
        0 0 20px var(--color),
        0 0 40px var(--color),
        0 0 80px var(--color);
    border-radius: 999px;
    transition: width 1.2s ease;
}

/* LEADER BOOST */
.row.leader .fill {
    box-shadow:
        0 0 30px var(--color),
        0 0 60px var(--color),
        0 0 120px var(--color);
}

/* SHIMMER */
.fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -50%;
    height: 100%;
    width: 50%;
    background: linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,0.4),
        transparent
    );
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { left: -50%; }
    100% { left: 150%; }
}

/* RIGHT PANEL */
.activity-title {
    font-size: 2.5vh;
    font-weight: bold;
    margin-bottom: 20px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow: hidden;
}

.activity-item {
    background: rgba(255,255,255,0.05);
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 1.8vh;
}
</style>
</head>
<body>

@php
$max = max($houses->pluck('points')->toArray());
@endphp

<div class="wrapper">

    <!-- LEFT SIDE -->
    <div class="left">

        <div class="title">House Points Race</div>

        <div class="race">

            @foreach($houses as $index => $house)

            @php
            $percent = $max > 0 ? ($house->points / $max) * 100 : 0;
            @endphp

            <div class="row {{ $index === 0 ? 'leader' : '' }}"
                 style="--color: {{ $house->colour_hex }};">

                <div class="label">
                    {{ $house->name }}
                </div>

                <div class="bar">
                    <div class="fill" data-width="{{ $percent }}"></div>
                </div>

                <div class="score">
                    {{ $house->points }}
                </div>

            </div>

            @endforeach

        </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right">

        <div class="activity-title">Live Activity</div>

        <div class="activity-list">
            @foreach($recent ?? [] as $item)
                <div class="activity-item">
                    +{{ $item->amount }}
                    {{ $item->house_name ?? 'House' }}
                    @if($item->first_name)
                        – {{ $item->first_name }}
                    @endif
                </div>
            @endforeach
        </div>

    </div>

</div>

<script>
// Animate bars
window.addEventListener('load', () => {
    document.querySelectorAll('.fill').forEach(el => {
        const width = el.getAttribute('data-width');
        setTimeout(() => {
            el.style.width = width + '%';
        }, 200);
    });
});
</script>

</body>
</html>