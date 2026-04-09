@extends('layouts.app')

@section('content')

<div class="dashboard-wrapper">

    <div class="dashboard">

        <!-- 🏆 HOUSE LEADERBOARD -->
        <div class="section">
            <h2 class="section-title">House Leaderboard</h2>

            <div class="house-grid">
                @foreach($houses as $index => $house)
                    <div class="house-card {{ $index == 0 ? 'winner' : '' }}"
                         style="--house-color: {{ $house->colour_hex }}"
                         onclick="selectHouse('{{ $house->name }}', this)">

                        <div class="house-header"></div>

                        <!-- 🔥 ADDED: CLEAR NAME -->
                        <div class="house-title">
                            #{{ $index + 1 }} {{ $house->name }}
                        </div>

                        <!-- 🔥 ADDED: BIG POINTS -->
                        <div class="house-points">
                            {{ number_format($house->points) }}
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        <!-- 🔥 HOUSE DETAIL PANEL -->
        <div id="house-detail" class="house-detail hidden">

            <div class="detail-header">
                <h3 id="detail-title">Select a house</h3>
            </div>

            <div class="detail-grid">

                <div class="detail-card">
                    <h4>Top Students</h4>
                    <div id="detail-students"></div>
                </div>

                <div class="detail-card">
                    <h4>Recent Activity</h4>
                    <div id="detail-activity"></div>
                </div>

            </div>

        </div>

        <!-- 👥 + ⚡ -->
        <div class="row-2">

            <div class="card">
                <h3>Top Students</h3>

                @foreach($topStudents as $student)
                    <div class="list-row">
                        <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                        <strong>{{ $student->house_points }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <h3>Recent Activity</h3>

                @foreach($recent as $r)
                    <div class="list-row">
                        <span>{{ $r->first_name ?? '' }} {{ $r->last_name ?? $r->house_name }}</span>
                        <strong class="{{ $r->amount > 0 ? 'plus' : 'minus' }}">
                            {{ $r->amount > 0 ? '+' : '' }}{{ $r->amount }}
                        </strong>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- 📊 + 👨‍🏫 -->
        <div class="row-2">

            <div class="card">
                <h3>Stats</h3>

                <div class="stat-row">
                    <div>
                        <div class="stat-label">Today</div>
                        <div class="stat-value">{{ $pointsToday }}</div>
                    </div>

                    <div>
                        <div class="stat-label">This Week</div>
                        <div class="stat-value">{{ $pointsWeek }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Top Teacher</h3>

                @if($topTeacher)
                    <div class="teacher-box">
                        <div class="teacher-name">{{ $topTeacher->name }}</div>
                        <div class="teacher-points">{{ $topTeacher->total_points }}</div>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection


<style>

/* 🔥 DARKER PREMIUM BACKGROUND */
.dashboard-wrapper {
    background: #0b0f19;
    padding: 30px;
    min-height: calc(100vh - 60px);
}

.dashboard {
    max-width: 1400px;
    margin: 0 auto;
}

/* TITLE */
.section-title {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 15px;
    color: white;
}

/* GRID */
.house-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* HOUSE CARD */
.house-card {
    background: #111827;
    border-radius: 14px;
    padding: 20px;
    border: 1px solid #1f2937;
    cursor: pointer;
    transition: all 0.25s ease;
    color: white;
}

.house-card:hover {
    transform: translateY(-3px);
}

/* WINNER */
.house-card.winner {
    border: 2px solid gold;
}

/* ACTIVE */
.house-card.active {
    background: #020617;
    border: 1px solid var(--house-color);
    box-shadow: 0 0 15px var(--house-color);
}

/* HEADER LINE */
.house-header {
    height: 5px;
    background: var(--house-color);
    border-radius: 6px;
    margin-bottom: 10px;
}

/* TEXT */
.house-title {
    font-weight: 700;
    font-size: 14px;
    opacity: 0.8;
}

.house-points {
    font-size: 30px;
    font-weight: 900;
}

/* DETAIL PANEL */
.house-detail {
    margin-top: 20px;
    padding: 20px;
    border-radius: 14px;
    background: #020617;
    color: white;
    transform: translateY(10px);
    opacity: 0;
    transition: all 0.3s ease;
}

.house-detail.show {
    transform: translateY(0);
    opacity: 1;
}

.hidden {
    display: none;
}

/* DETAIL GRID */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* DETAIL CARDS */
.detail-card {
    background: #0f172a;
    padding: 15px;
    border-radius: 10px;
}

/* ROWS */
.row-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 25px;
}

/* CARDS */
.card {
    background: #111827;
    padding: 22px;
    border-radius: 14px;
    border: 1px solid #1f2937;
    color: white;
}

/* LIST */
.list-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
}

/* STATS */
.stat-row {
    display: flex;
    justify-content: space-between;
}

.stat-value {
    font-size: 26px;
    font-weight: 900;
}

/* TEACHER */
.teacher-box {
    display: flex;
    justify-content: space-between;
}

.plus { color: #16a34a; }
.minus { color: #dc2626; }

</style>


<script>

const activity = @json($recent ?? []);

function selectHouse(houseName, el) {

    document.querySelectorAll('.house-card')
        .forEach(c => c.classList.remove('active'));

    el.classList.add('active');

    const panel = document.getElementById('house-detail');
    panel.classList.remove('hidden');

    setTimeout(() => panel.classList.add('show'), 10);

    document.getElementById('detail-title').innerText = houseName;

    const filtered = activity.filter(a => a.house_name === houseName);

    let studentHTML = '';
    let activityHTML = '';

    filtered.slice(0,5).forEach(a => {
        studentHTML += `<div class="list-row">
            <span>${a.first_name ?? ''} ${a.last_name ?? ''}</span>
            <strong>${a.amount > 0 ? '+' : ''}${a.amount}</strong>
        </div>`;
    });

    filtered.slice(0,5).forEach(a => {
        activityHTML += `<div class="list-row">
            <span>${a.first_name ?? ''} ${a.last_name ?? ''}</span>
            <strong class="${a.amount > 0 ? 'plus' : 'minus'}">
                ${a.amount > 0 ? '+' : ''}${a.amount}
            </strong>
        </div>`;
    });

    document.getElementById('detail-students').innerHTML = studentHTML;
    document.getElementById('detail-activity').innerHTML = activityHTML;
}

// 🔥 AUTO SELECT FIRST HOUSE
document.addEventListener('DOMContentLoaded', function () {
    const firstHouse = document.querySelector('.house-card');
    if (firstHouse) {
        firstHouse.click();
    }
});

</script>