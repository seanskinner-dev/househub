<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>House Points This Week</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    height: 100vh;
    background: linear-gradient(to bottom, #87CEEB, #e0f2fe);
    font-family: Arial, sans-serif;
    color: #111;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* TITLE */
.title {
    text-align: center;
    font-size: 6vh;
    margin: 2vh 0;
    font-weight: bold;
}

/* WRAPPER */
.chart-wrapper {
    flex: 1;
    padding: 2vh 3vw;
}

.chart-card {
    height: 82vh;
    background: rgba(255,255,255,0.9);
    border-radius: 20px;
    padding: 30px;

    box-shadow:
        0 10px 40px rgba(0,0,0,0.15);
}

canvas {
    width: 100% !important;
    height: 100% !important;
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
    font-size: 16px;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;

    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}
</style>
</head>

<body>

@php
$slytherin = $slytherin ?? [0,0,0,0,0];
$hufflepuff = $hufflepuff ?? [0,0,0,0,0];
$ravenclaw = $ravenclaw ?? [0,0,0,0,0];
$gryffindor = $gryffindor ?? [0,0,0,0,0];
@endphp

<div class="title">House Points This Week</div>

<div class="chart-wrapper">
    <div class="chart-card">
        <canvas id="chart"></canvas>
    </div>
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
(function () {

    const slytherin = @json($slytherin);
    const hufflepuff = @json($hufflepuff);
    const ravenclaw = @json($ravenclaw);
    const gryffindor = @json($gryffindor);

    function normalize(data) {
        let fixed = Array.isArray(data) ? data.slice() : [0,0,0,0,0];
        while (fixed.length < 5) fixed.push(0);
        return fixed.slice(0,5).map(v => Number(v) || 0);
    }

    const sly = normalize(slytherin);
    const huf = normalize(hufflepuff);
    const rav = normalize(ravenclaw);
    const gry = normalize(gryffindor);

    function total(arr) {
        return arr.reduce((a,b) => a+b, 0);
    }

    const totals = {
        sly: total(sly),
        huf: total(huf),
        rav: total(rav),
        gry: total(gry)
    };

    const leader = Object.keys(totals).reduce((a, b) => totals[a] > totals[b] ? a : b);

    function dataset(label, color, data, key) {
        const isLeader = key === leader;

        return {
            label: label,
            data: data,
            borderColor: color,
            backgroundColor: color.replace('1)', '0.25)'),
            fill: true,
            tension: 0.4,
            borderWidth: isLeader ? 6 : 4,
            pointRadius: 0
        };
    }

    const ctx = document.getElementById('chart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri'],
            datasets: [
                dataset('Slytherin 🐍', 'rgba(34,197,94,1)', sly, 'sly'),
                dataset('Hufflepuff 🦡', 'rgba(250,204,21,1)', huf, 'huf'),
                dataset('Ravenclaw 🦅', 'rgba(59,130,246,1)', rav, 'rav'),
                dataset('Gryffindor 🦁', 'rgba(239,68,68,1)', gry, 'gry')
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#111',
                        font: {
                            size: 18,
                            weight: 'bold'
                        }
                    }
                }
            },

            scales: {
                x: {
                    ticks: {
                        color: '#333',
                        font: { size: 16 }
                    },
                    grid: { display: false }
                },
                y: {
                    ticks: {
                        color: '#333',
                        font: { size: 16 }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.08)'
                    }
                }
            }
        }
    });

})();
</script>

</body>
</html>