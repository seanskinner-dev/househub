<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Weather</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    height: 100vh;
    overflow: hidden;
    font-family: Arial;
    background: #f4f6f8;
    color: #111;
    display: flex;
    flex-direction: column;
}

/* TOP */
.top { padding: 3vh 4vw; }
.temp { font-size: 8vh; font-weight: bold; }
.desc { font-size: 2.5vh; opacity: 0.7; }

/* STATUS */
.lunch-status {
    text-align: center;
    font-size: 3.5vh;
    font-weight: bold;
    padding: 1vh;
    border-radius: 10px;
    margin: 1vh 3vw;
}

.lunch-dry { background: #d4edda; color: #155724; }
.lunch-wet { background: #f8d7da; color: #721c24; animation: pulse 2s infinite; }

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* FORECAST */
.forecast {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    padding: 2vh 2vw;
    gap: 1vw;
}

.day { text-align: center; }
.day-name { font-size: 1.8vh; }
.day-temp { font-size: 2.2vh; }
.day-min { font-size: 1.5vh; opacity: 0.5; }

/* GRAPH */
.graph {
    height: 30vh;
    min-height: 250px;
    padding: 1vh 2vw;
}

/* LEGEND */
.legend {
    display: flex;
    justify-content: center;
    gap: 3vw;
    font-size: 1.8vh;
    margin-bottom: 1vh;
    opacity: 0.7;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.6vw;
}

.legend-line { width: 20px; height: 3px; background: #222; }
.legend-rain { width: 20px; height: 10px; background: rgba(33,150,243,0.3); }
.legend-break { width: 20px; height: 10px; background: rgba(255,80,80,0.6); }

canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>
</head>

<body>

<div class="top">
    <div class="temp" id="temp">--°</div>
    <div class="desc" id="desc">Loading...</div>
</div>

<div id="lunchStatus" class="lunch-status">Checking breaks...</div>

<div class="forecast" id="forecast"></div>

<div class="graph">
    <div class="legend">
        <div class="legend-item"><span class="legend-line"></span> Temperature</div>
        <div class="legend-item"><span class="legend-rain"></span> Rain %</div>
        <div class="legend-item"><span class="legend-break"></span> Break Risk</div>
    </div>
    <canvas id="chart"></canvas>
</div>

<script>

let chart = null;

// 🌧️ Rain intensity
function getRainType(mm) {
    if (mm > 2) return "heavy";
    if (mm > 0.5) return "moderate";
    if (mm > 0) return "light";
    return null;
}

// 🔥 STATUS (UPDATED TO EXACT TIMES)
function updateLunchStatus(labels, rain, rainMM) {

    let wetRecess = false;
    let wetLunch = false;
    let worstType = null;

    labels.forEach((h, i) => {

        const type = getRainType(rainMM[i]);

        // ✅ EXACT TIMES
        if (h === 10 && rain[i] > 30) {
            wetRecess = true;
            if (type === "heavy") worstType = "heavy";
            else if (type === "moderate" && worstType !== "heavy") worstType = "moderate";
            else if (!worstType) worstType = "light";
        }

        if (h === 13 && rain[i] > 30) {
            wetLunch = true;
            if (type === "heavy") worstType = "heavy";
            else if (type === "moderate" && worstType !== "heavy") worstType = "moderate";
            else if (!worstType) worstType = "light";
        }
    });

    const el = document.getElementById('lunchStatus');

    let emoji = "🌧️";
    if (worstType === "heavy") emoji = "⛈";
    if (worstType === "light") emoji = "🌦";

    if (wetRecess && wetLunch) {
        el.innerText = `${emoji} Wet recess & lunch (${worstType} rain)`;
        el.className = "lunch-status lunch-wet";
    }
    else if (wetRecess) {
        el.innerText = `${emoji} Wet recess (${worstType} rain)`;
        el.className = "lunch-status lunch-wet";
    }
    else if (wetLunch) {
        el.innerText = `${emoji} Wet lunch (${worstType} rain)`;
        el.className = "lunch-status lunch-wet";
    }
    else {
        el.innerText = "☀️ Dry breaks";
        el.className = "lunch-status lunch-dry";
    }
}

// 🎨 COLOUR LOGIC (UPDATED)
function getRainColours(labels, rain) {
    return rain.map((v, i) => {
        const h = labels[i];

        const isRecess = (h === 10);
        const isLunch = (h === 13);

        if ((isRecess || isLunch) && v > 30) {
            return 'rgba(255,80,80,0.6)';
        }

        if (v > 30) return 'rgba(33,150,243,0.4)';
        return 'rgba(33,150,243,0.15)';
    });
}

// 📊 CREATE
function createChart(labels, temps, rain) {

    const ctx = document.getElementById('chart').getContext('2d');

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    data: temps,
                    borderColor: '#222',
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 4
                },
                {
                    data: rain,
                    type: 'bar',
                    backgroundColor: getRainColours(labels, rain),
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 600 },
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    ticks: {
                        callback: function(value) {
                            const hour = labels[value];

                            const h = hour % 12 || 12;
                            const ampm = hour < 12 ? 'am' : 'pm';

                            if (hour === 10) return 'Recess';
                            if (hour === 13) return 'Lunch';

                            return h + ampm;
                        }
                    }
                },
                y: {
                    ticks: { callback: v => v + '°' }
                },
                y1: {
                    display: false,
                    min: 0,
                    max: 100
                }
            }
        }
    });
}

// UPDATE
function updateChart(labels, temps, rain) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = temps;
    chart.data.datasets[1].data = rain;
    chart.data.datasets[1].backgroundColor = getRainColours(labels, rain);
    chart.update();
}

// LOAD
async function loadWeather() {

    const res = await fetch(
        'https://api.open-meteo.com/v1/forecast?latitude=-42.81&longitude=147.25&hourly=temperature_2m,precipitation_probability,precipitation&daily=temperature_2m_max,temperature_2m_min&current_weather=true'
    );

    const data = await res.json();

    document.getElementById('temp').innerText =
        Math.round(data.current_weather.temperature) + '°';

    document.getElementById('desc').innerText = 'Current conditions';

    let forecastHTML = '';

    data.daily.time.forEach((d, i) => {
        const date = new Date(d);
        const day = date.getDay();
        if (day === 0 || day === 6) return;

        const name = date.toLocaleDateString('en-AU', { weekday: 'short' });

        forecastHTML += `
            <div class="day">
                <div class="day-name">${name}</div>
                <div class="day-temp">${Math.round(data.daily.temperature_2m_max[i])}°</div>
                <div class="day-min">${Math.round(data.daily.temperature_2m_min[i])}°</div>
            </div>
        `;
    });

    document.getElementById('forecast').innerHTML = forecastHTML;

    const labels = [], temps = [], rain = [], rainMM = [];
    const today = new Date().getDate();

    data.hourly.time.forEach((t, i) => {
        const d = new Date(t);
        if (d.getDate() === today) {
            const h = d.getHours();
            if (h >= 8 && h <= 16) {
                labels.push(h);
                temps.push(data.hourly.temperature_2m[i]);
                rain.push(data.hourly.precipitation_probability[i] || 0);
                rainMM.push(data.hourly.precipitation[i] || 0);
            }
        }
    });

    if (!chart) createChart(labels, temps, rain);
    else updateChart(labels, temps, rain);

    updateLunchStatus(labels, rain, rainMM);
}

document.addEventListener("DOMContentLoaded", () => {
    loadWeather();
    setInterval(loadWeather, 300000);
});

</script>

</body>
</html>