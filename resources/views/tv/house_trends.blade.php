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
    background: radial-gradient(circle at center, #01040a 0%, #000 100%);
    font-family: Arial, sans-serif;
    color: white;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 🔥 UPGRADED TITLE */
.title {
    text-align: center;
    font-size: 5vh;
    margin: 2vh 0;
    font-weight: bold;
    letter-spacing: 0.1vw;

    text-shadow:
        0 0 20px rgba(255,255,255,0.2);
}

.chart-wrapper {
    flex: 1;
    padding: 2vh 3vw;
}

.chart-card {
    height: 80vh;
    background: #020617;
    border-radius: 20px;
    padding: 30px;

    box-shadow:
        0 0 60px rgba(0,0,0,0.9),
        inset 0 0 20px rgba(255,255,255,0.02);
}

/* 🔥 HARD LOCK CANVAS */
canvas {
    width: 100% !important;
    height: 100% !important;
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

<script>
(function () {

    if (window.houseTrendChartLoaded) return;
    window.houseTrendChartLoaded = true;

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
        Slytherin: total(sly),
        Hufflepuff: total(huf),
        Ravenclaw: total(rav),
        Gryffindor: total(gry)
    };

    let leader = 'Slytherin';
    Object.keys(totals).forEach(key => {
        if (totals[key] > totals[leader]) leader = key;
    });

    // 🔥 PREMIUM STYLE
    function getStyle(name, color, data) {
        const isLeader = name === leader;

        return {
            label: name,
            data: data,
            borderColor: color,
            backgroundColor: color + '22',
            borderWidth: isLeader ? 6 : 3,
            tension: 0.4,
            pointRadius: isLeader ? 4 : 0,
            pointHoverRadius: 6,
            pointBackgroundColor: color,
            fill: true
        };
    }

    const canvas = document.getElementById('chart');

    if (window.chartInstance) {
        try { window.chartInstance.destroy(); } catch(e){}
    }

    // 🔥 GLOW PLUGIN (BIG VISUAL BOOST)
    const glowPlugin = {
        id: 'glow',
        beforeDatasetsDraw(chart) {
            const {ctx} = chart;

            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);

                ctx.save();
                ctx.shadowColor = dataset.borderColor;
                ctx.shadowBlur = 20;
                ctx.lineWidth = dataset.borderWidth;

                meta.dataset.draw(ctx);

                ctx.restore();
            });
        }
    };

    window.chartInstance = new Chart(canvas, {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri'],
            datasets: [
                getStyle('Slytherin', '#22c55e', sly),
                getStyle('Hufflepuff', '#facc15', huf),
                getStyle('Ravenclaw', '#3b82f6', rav),
                getStyle('Gryffindor', '#ef4444', gry)
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            animation: false,

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#fff',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: 20
                    }
                }
            },

            scales: {
                x: {
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 14 }
                    },
                    grid: {
                        display: false
                    }
                },

                y: {
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 14 }
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.04)'
                    }
                }
            }
        },
        plugins: [glowPlugin]
    });

})();
</script>

</body>
</html>