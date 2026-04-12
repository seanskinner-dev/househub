<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseHub TV</title>
    <style>
        body {
            margin: 0;
        }

/* BASE */
.tv-container {
    height: 100vh;
    width: 100vw;
    background: #0f172a;
    color: white;
}

.tv-broadcast-banner {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: #dc2626;
    color: white;
    text-align: center;
    font-size: 40px;
    font-weight: bold;
    padding: 20px;
    display: none;
    z-index: 9999;
}

#emergencyScreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: #dc2626;
    color: white;

    align-items: center;
    justify-content: center;

    font-size: 80px;
    font-weight: bold;
    text-align: center;

    z-index: 10000;
    padding: 40px;
}

/* FIXED SCREEN HEIGHT */
.tv-screen {
    display: none;
    height: 100vh;
    width: 100%;
}

/* GRAPH FIX */
.graph-wrapper {
    height: 100%;
    width: 100%;
    padding: 40px 60px;
    display: flex;
    flex-direction: column;
}

#trendChart {
    flex: 1;
}

/* TITLE */
.students-title {
    font-size: 48px;
    text-align: center;
    margin-bottom: 20px;
}

/* 2 COLUMN */
.students-grid {
    display: grid !important;
    grid-template-columns: 1fr 1fr !important;
    gap: 14px;
    padding: 0 40px;
}

/* BUTTON STYLE */
.student-row {
    display: flex !important;
    justify-content: space-between;
    align-items: center;

    padding: 12px 16px;
    border-radius: 8px;

    font-size: 20px;
    font-weight: 600;
}

/* HOUSE COLOURS */
.student-row.gryffindor { background: #740001; }
.student-row.slytherin  { background: #1a472a; }
.student-row.ravenclaw  { background: #1e40af; }
.student-row.hufflepuff { background: #ffcc00; color:#111; }

/* POINTS */
.student-row .points {
    font-weight: bold;
}

.next-btn {
    position: absolute;
    bottom: 20px;
    right: 20px;
}

.center {
    display: flex;
    align-items: center;
    justify-content: center;
}

.list {
    padding: 40px;
}

.list h1 {
    font-size: 60px;
    text-align: center;
}

.list .row {
    display: flex;
    justify-content: space-between;
    font-size: 36px;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.list .row.gryffindor { background: #740001; }
.list .row.slytherin  { background: #1a472a; }
.list .row.ravenclaw  { background: #1e40af; }
.list .row.hufflepuff { background: #ffcc00; color: #111; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body>

<div class="tv-container">

    <div id="emergencyScreen" style="display:none;">
        <div id="emergencyText"></div>
    </div>

    <div id="broadcastBanner" class="tv-broadcast-banner" role="status" aria-live="polite"></div>

    <!-- SCREEN 1 -->
    <div class="tv-screen" id="screen-1">
        <div class="row h-100">
            @foreach($series as $index => $house)
                <div class="col-3 d-flex">
                    <div class="house-card w-100 text-center"
                         style="background: {{ $house['color'] }}">
                        <div class="rank">#{{ $index + 1 }}</div>
                        <h2 class="house-name">{{ $house['name'] }}</h2>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- SCREEN 2 — WEATHER -->
    <div class="tv-screen" id="screen-2">
        <div class="center" style="font-size:80px;">
            🌧️ 12°C
        </div>
    </div>

    <!-- SCREEN 3 — TOP STUDENTS (POINTS) -->
    <div class="tv-screen" id="screen-3">
        <div class="list">
            <h1>Top Students</h1>

            @foreach($topStudents as $index => $student)
                <div class="row {{ strtolower($student->house_name ?? 'gryffindor') }}">
                    <span>#{{ $index + 1 }} {{ $student->first_name }}</span>
                    <span>{{ $student->total }}</span>
                </div>
            @endforeach

        </div>
    </div>

    <!-- SCREEN 4 — COMMENDATIONS -->
    <div class="tv-screen" id="screen-4">
        <div class="list">
            <h1>Commendations</h1>

            @foreach($topStudents as $index => $student)
                <div class="row {{ strtolower($student->house_name ?? 'gryffindor') }}">
                    <span>#{{ $index + 1 }} {{ $student->first_name }}</span>
                    <span>{{ $student->commendations ?? 0 }}</span>
                </div>
            @endforeach

        </div>
    </div>

    <!-- SCREEN 5 — STREAKS -->
    <div class="tv-screen" id="screen-5">
        <div class="list">
            <h1>🔥 On a Streak</h1>

            @foreach($topStudents as $index => $student)
                <div class="row {{ strtolower($student->house_name ?? 'gryffindor') }}">
                    <span>{{ $student->first_name }}</span>
                    <span>{{ rand(2, 7) }} days</span>
                </div>
            @endforeach

        </div>
    </div>

    <!-- SCREEN 6 — LIVE ACTIVITY -->
    <div class="tv-screen" id="screen-6">
        <div class="list">
            <h1>Live Activity</h1>

            @foreach($recent ?? [] as $item)
                <div class="row">
                    <span>{{ $item->description ?? 'Activity' }}</span>
                    <span>+{{ $item->amount ?? 1 }}</span>
                </div>
            @endforeach

        </div>
    </div>

    <!-- SCREEN 7 — TEACHERS -->
    <div class="tv-screen" id="screen-7">
        <div class="list">
            <h1>Top Teachers</h1>

            @foreach($topTeachers as $index => $teacher)
                <div class="row">
                    <span>#{{ $index + 1 }} {{ $teacher->name }}</span>
                    <span>{{ $teacher->total }}</span>
                </div>
            @endforeach

        </div>
    </div>

    <!-- SCREEN 8 — HOUSE POINTS TREND (chart) -->
    <div class="tv-screen" id="screen-8">
        <div class="graph-wrapper">

            <div class="graph-title">
                House Points Trend
            </div>

            <div class="house-legend">
                <div>🦁 Gryffindor</div>
                <div>🐍 Slytherin</div>
                <div>🦅 Ravenclaw</div>
                <div>🦡 Hufflepuff</div>
            </div>

            <div id="trendChart"></div>

        </div>
    </div>

    <button id="nextBtn" class="next-btn">Next ▶</button>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let currentScreen = 0;
    const screens = document.querySelectorAll('.tv-screen');
    const trendChartEl = document.querySelector('#trendChart');
    const chartHost = trendChartEl ? trendChartEl.closest('.tv-screen') : null;
    const chartScreenIndex = chartHost ? Array.prototype.indexOf.call(screens, chartHost) : -1;

    let chartRendered = false;
    const broadcastUrl = @json(route('broadcast-messages.latest'));
    const broadcastBanner = document.getElementById('broadcastBanner');

    function showScreen(index) {
        screens.forEach((s, i) => {
            s.style.display = (i === index) ? 'block' : 'none';
        });
    }

    function nextScreen() {
        currentScreen = (currentScreen + 1) % screens.length;
        showScreen(currentScreen);

        if (chartScreenIndex !== -1 && currentScreen === chartScreenIndex && !chartRendered) {
            renderChart();
            chartRendered = true;
        }
    }

    document.getElementById('nextBtn').addEventListener('click', nextScreen);
    setInterval(nextScreen, 10000);
    showScreen(0);

    setTimeout(() => {
        showScreen(0);
    }, 100);

    function renderChart() {
        new ApexCharts(document.querySelector("#trendChart"), {
            chart: {
                type: 'area',
                height: '100%',
                toolbar: { show: false }
            },
            series: @json($series),
            colors: ['#740001','#1a472a','#0e1a40','#ffcc00'],
            stroke: { curve: 'smooth', width: 5 },
            xaxis: { categories: @json($dates) },
            yaxis: { min: 0 },
            legend: { show: false }
        }).render();
    }

    function fetchBroadcast() {
        const emergencyScreen = document.getElementById('emergencyScreen');
        const emergencyText = document.getElementById('emergencyText');

        fetch(broadcastUrl)
            .then(function (res) {
                if (!res.ok) throw new Error('broadcast fetch failed');
                return res.json();
            })
            .then(function (data) {
                const message = data && data.message ? String(data.message) : '';

                if (message && message.startsWith('EMERGENCY:')) {
                    if (emergencyScreen) {
                        emergencyScreen.style.display = 'flex';
                    }
                    if (emergencyText) {
                        emergencyText.innerText = message.slice('EMERGENCY:'.length).trim();
                    }
                    if (broadcastBanner) {
                        broadcastBanner.style.display = 'none';
                    }
                } else {
                    if (emergencyScreen) {
                        emergencyScreen.style.display = 'none';
                    }
                    if (broadcastBanner) {
                        if (message) {
                            broadcastBanner.textContent = message;
                            broadcastBanner.style.display = 'block';
                        } else {
                            broadcastBanner.style.display = 'none';
                        }
                    }
                }
            })
            .catch(function () {
                if (broadcastBanner) {
                    broadcastBanner.style.display = 'none';
                }
            });
    }

    fetchBroadcast();
    setInterval(fetchBroadcast, 5000);
});
</script>

</body>
</html>
