@extends('layouts.app')

@section('content')

<div class="tv-container">

    <!-- SCREEN 1: HOUSE LEADERBOARD -->
    <div class="tv-screen active" id="screen-1">
        <div class="row h-100">
            @foreach($houses as $index => $house)
                <div class="col-3 d-flex">
                    <div class="house-card w-100 text-center {{ $index == 0 ? 'winner' : '' }}"
                         style="background: {{ $house->colour_hex }}">

                        <div class="rank">
                            #{{ $index + 1 }}
                        </div>

                        <h2 class="house-name">{{ $house->name }}</h2>

                        <div class="points" data-points="{{ $house->points }}">
                            0
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SCREEN 2: LIVE ACTIVITY -->
    <div class="tv-screen" id="screen-2">
        <div class="activity-grid">
            @foreach($activity->take(8) as $item)
                <div class="activity-row">
                    <div class="student">
                        {{ $item->first_name }} {{ $item->last_name }}
                    </div>

                    <div class="points-change {{ $item->amount > 0 ? 'plus' : 'minus' }}">
                        {{ $item->amount > 0 ? '+' : '' }}{{ $item->amount }}
                    </div>

                    <div class="desc">
                        {{ $item->description ?? '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- NEXT BUTTON -->
    <button id="nextBtn" class="next-btn">Next ▶</button>

</div>

@endsection


@push('scripts')

<script>

// ===============================
// SCREEN CONTROL
// ===============================
let currentScreen = 0;
const screens = document.querySelectorAll('.tv-screen');

function showScreen(index) {
    screens.forEach((s, i) => {
        s.classList.toggle('active', i === index);
    });
}

function nextScreen() {
    currentScreen = (currentScreen + 1) % screens.length;
    showScreen(currentScreen);
}

document.getElementById('nextBtn').addEventListener('click', nextScreen);
setInterval(nextScreen, 10000);
showScreen(0);


// ===============================
// COUNT-UP ANIMATION (SMOOTH)
// ===============================
document.querySelectorAll('.points').forEach(el => {
    let target = parseInt(el.dataset.points);
    let count = 0;

    let step = Math.max(1, Math.ceil(target / 40));

    let interval = setInterval(() => {
        count += step;
        if (count >= target) {
            count = target;
            clearInterval(interval);
        }
        el.innerText = count;
    }, 25);
});

</script>

@endpush


<style>

.tv-container {
    height: 100vh;
    background: #0f172a;
    color: white;
    overflow: hidden;
}

/* SCREENS */
.tv-screen {
    display: none;
    height: 100%;
}

.tv-screen.active {
    display: block;
}

/* HOUSE CARDS */
.house-card {
    padding: 40px;
    border-radius: 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: all 0.4s ease;
}

/* WINNER EFFECT */
.house-card.winner {
    transform: scale(1.08);
    box-shadow: 0 0 30px rgba(255,255,255,0.4);
}

/* RANK */
.rank {
    font-size: 36px;
    font-weight: bold;
    opacity: 0.9;
}

/* NAME */
.house-name {
    font-size: 32px;
    margin: 15px 0;
}

/* POINTS */
.points {
    font-size: 64px;
    font-weight: bold;
}

/* ACTIVITY */
.activity-grid {
    display: grid;
    grid-template-rows: repeat(8, 1fr);
    gap: 12px;
    padding: 20px;
}

.activity-row {
    display: flex;
    justify-content: space-between;
    font-size: 22px;
}

.points-change.plus {
    color: #22c55e;
}

.points-change.minus {
    color: #ef4444;
}

/* BUTTON */
.next-btn {
    position: absolute;
    bottom: 20px;
    right: 20px;
    padding: 12px 20px;
    background: #2563eb;
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
}

</style>