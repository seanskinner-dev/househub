<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>House Points by Year</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            background: radial-gradient(circle at center, #111 0%, #000 100%);
            color: white;
            font-family: Arial, sans-serif;
        }

        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 40px;
        }

        h1 {
            text-align: center;
            font-size: 56px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .chart-wrapper {
            flex-grow: 1;
            position: relative;
        }

        canvas {
            width: 100% !important;
            height: 80vh !important;
        }

        .debug {
            color: red;
            text-align: center;
            font-size: 24px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>House Points by Year</h1>

    <div class="chart-wrapper">
        <canvas id="yearChart"></canvas>
    </div>

    <div id="debug" class="debug"></div>
</div>

<script>
const rawData = @json($data);

console.log("YEAR DATA:", rawData);

// 🚨 No data check
if (!rawData || rawData.length === 0) {
    document.getElementById('debug').innerText = "NO DATA FOUND";
}

// GROUP DATA
const grouped = {};
const labelsSet = new Set();

rawData.forEach(row => {
    if (!row.year) return;

    const year = parseInt(row.year);

    labelsSet.add(year);

    if (!grouped[row.name]) {
        grouped[row.name] = {
            colour: row.colour_hex || '#ffffff',
            data: {}
        };
    }

    grouped[row.name].data[year] = row.total;
});

// 🔥 Ensure at least 2 points (line graph fix)
if (labelsSet.size === 1) {
    const onlyYear = [...labelsSet][0];
    labelsSet.add(onlyYear - 1);
}

const labels = Array.from(labelsSet).sort((a, b) => a - b);

// DATASETS
const datasets = Object.keys(grouped).map(house => {
    return {
        label: house,
        data: labels.map(year => grouped[house].data[year] || 0),
        borderColor: grouped[house].colour,
        backgroundColor: grouped[house].colour,
        borderWidth: 5,
        tension: 0.3,
        pointRadius: 3
    };
});

// 🚨 Safety
if (datasets.length === 0) {
    document.getElementById('debug').innerText = "DATA STRUCTURE INVALID";
}

// CONTEXT
const ctx = document.getElementById('yearChart').getContext('2d');

// GLOW EFFECT
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

        plugins: {
            legend: {
                labels: {
                    color: '#fff',
                    font: { size: 22 }
                }
            }
        },

        scales: {
            x: {
                ticks: {
                    color: '#ccc',
                    font: { size: 18 }
                },
                title: {
                    display: true,
                    text: 'Year',
                    color: '#aaa',
                    font: { size: 22 }
                },
                grid: {
                    display: false
                }
            },
            y: {
                ticks: {
                    color: '#ccc',
                    font: { size: 18 }
                },
                title: {
                    display: true,
                    text: 'House Points',
                    color: '#aaa',
                    font: { size: 22 }
                },
                grid: {
                    color: 'rgba(255,255,255,0.05)'
                }
            }
        }
    },
    plugins: [glowPlugin]
});
</script>

</body>
</html>