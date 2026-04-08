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

                        <!-- ✅ FIXED -->
                        <div class="points">
                            {{ $house->points }}
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SCREEN 2: LIVE ACTIVITY -->
    <div class="tv-screen" id="screen-2">
        <div class="activity-grid">
            @foreach($recent as $item)
                <div class="activity-row">
                    <div class="student">
                        {{ $item->first_name ?? '' }} {{ $item->last_name ?? $item->house_name }}
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

    <!-- SCREEN 3: TOP TEACHER -->
    <div class="tv-screen" id="screen-3">
        <div class="row h-100">
            <div class="col-12 text-center">
                <h2>Top Teacher</h2>

                @if($topTeacher)
                    <h3>{{ $topTeacher->name }}</h3>
                    <div class="points">
                        {{ $topTeacher->total_points }} points
                    </div>
                @else
                    <h3>No data</h3>
                @endif

            </div>
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

.house-card.winner {
    transform: scale(1.08);
    box-shadow: 0 0 30px rgba(255,255,255,0.4);
}

.rank {
    font-size: 36px;
    font-weight: bold;
}

.house-name {
    font-size: 32px;
    margin: 15px 0;
}

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
}

/* TOP TEACHER */
#screen-3 {
    background: #1e3a8a;
    text-align: center;
}

#screen-3 .points {
    font-size: 48px;
    font-weight: bold;
    margin-top: 10px;
}

</style>