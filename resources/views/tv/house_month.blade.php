<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Points This Month</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    height: 100vh;
    background: linear-gradient(to bottom, #87CEEB, #e0f2fe);
    color: #111;
    font-family: Arial, sans-serif;
    overflow: hidden;
}

.container {
    height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 40px;
}

/* TITLE */
h1 {
    text-align: center;
    font-size: 6vh;
    margin-bottom: 20px;
    font-weight: bold;
}

/* CARD */
.chart-wrapper {
    flex-grow: 1;
    position: relative;
    background: rgba(255,255,255,0.9);
    border-radius: 20px;
    padding: 25px;

    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}

canvas {
    width: 100% !important;
    height: 75vh !important;
}

/* DEBUG */
.debug {
    color: red;
    text-align: center;
    font-size: 20px;
    margin-top: 10px;
}

/* NEXT BUTTON */
.next-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;

    background: white;
    color: black;
    border: none;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;

    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
</style>
</head>

<body>

<div class="container">

    <h1>House Points This Month</h1>

    <div class="chart-wrapper">
        <canvas id="monthChart"></canvas>
    </div>

    <div id="debug" class="debug"></div>

</div>

<button class="next-btn" onclick="nextSlide()">Next ▶</button>

<script>
function nextSlide() {
    if (window.parent && window.parent.manualNext) {
        window.parent.manualNext();
    }
}
</script>

<script>
const rawData = @json($data);

console.log("RAW DATA:", rawData);

// 🚨 HARD STOP if no data
if (!rawData || rawData.length === 0) {
    document.getElementById('debug').innerText = "NO DATA FOUND";
}

// GROUP DATA
const grouped = {};
const labelsSet = new Set();

rawData.forEach(row => {
    if (!row.date) return;

    const date = new Date(row.date);
    const day = date.getDate();

    labelsSet.add(day);

    if (!grouped[row.name]) {
        grouped[row.name] = {
            colour: row.colour_hex || '#999',
            data: {}
        };
    }

    grouped[row.name].data[day] = Number(row.total) || 0;
});

// 🔥 ensure minimum points
if (labelsSet.size === 1) {
    const onlyDay = [...labelsSet][0];
    labelsSet.add(onlyDay - 1);
}

const labels = Array.from(labelsSet).sort((a, b) => a - b);

// BUILD DATASETS (NOW WITH AREA FILL)
const datasets = Object.keys(grouped).map(house => {

    const houseData = labels.map(day => grouped[house].data[day] || 0);
    const colour = grouped[house].colour;

    return {
        label: house,
        data: houseData,
        borderColor: colour,
        backgroundColor: colour + '40', // 🔥 soft fill
        fill: true,
        borderWidth: 4,
        tension: 0.4,
        pointRadius: 0
    };
});

// 🚨 SECOND CHECK
if (datasets.length === 0) {
    document.getElementById('debug').innerText = "DATA STRUCTURE INVALID";
}

const ctx = document.getElementById('monthChart').getContext('2d');

// CHART
new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,

        plugins: {
            legend: {
                labels: {
                    color: '#111',
                    font: { size: 18, weight: 'bold' }
                }
            }
        },

        scales: {
            x: {
                ticks: {
                    color: '#333',
                    font: { size: 16 }
                },
                title: {
                    display: true,
                    text: 'Day of Month',
                    color: '#555',
                    font: { size: 18 }
                },
                grid: { display: false }
            },
            y: {
                ticks: {
                    color: '#333',
                    font: { size: 16 }
                },
                title: {
                    display: true,
                    text: 'House Points',
                    color: '#555',
                    font: { size: 18 }
                },
                grid: {
                    color: 'rgba(0,0,0,0.08)'
                }
            }
        }
    }
});
</script>

</body>
</html>