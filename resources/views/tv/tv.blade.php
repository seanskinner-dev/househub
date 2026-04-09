<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HouseHub TV</title>

<style>
body {
    margin: 0;
    overflow: hidden;
    background: black;
}

/* iframe */
iframe {
    width: 100vw;
    height: 100vh;
    border: none;
}

/* CONTROLS */
.controls {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    display: flex;
    gap: 10px;
}

/* BUTTONS */
.btn {
    background: rgba(0,0,0,0.6);
    color: white;
    border: 2px solid white;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: 14px;
    cursor: pointer;
}

/* SCREEN LABEL */
.label {
    position: fixed;
    top: 15px;
    left: 20px;
    z-index: 999;

    background: rgba(0,0,0,0.5);
    padding: 8px 14px;
    border-radius: 8px;

    font-size: 14px;
    color: white;
}
</style>
</head>

<body>

<div class="label" id="label">Loading...</div>

<iframe id="screen" src=""></iframe>

<div class="controls">
    <button class="btn" onclick="prevScreen()">← Prev</button>
    <button class="btn" onclick="nextScreen()">Next →</button>
</div>

<script>

const screens = [
    { url: '/tv/house-race-live', name: 'House Race' },
    { url: '/tv/live-activity', name: 'Live Activity' },
    { url: '/tv/daily-winner', name: 'Daily Winner' },
    { url: '/tv/hot-streak', name: 'Hot Streak' },
    { url: '/tv/weekly-winner', name: 'Weekly Winner' },
    { url: '/tv/teachers-top', name: 'Top Teachers' },
    { url: '/tv/top-students', name: 'Top Students' },
    { url: '/tv/house-total', name: 'House Totals' },
    { url: '/tv/weather', name: 'Weather' }
];

let index = 0;
let interval;

/* LOAD SCREEN */
function loadScreen() {
    document.getElementById('screen').src = screens[index].url;
    document.getElementById('label').innerText = screens[index].name;
}

/* NEXT */
function nextScreen() {
    index = (index + 1) % screens.length;
    loadScreen();
}

/* PREV */
function prevScreen() {
    index = (index - 1 + screens.length) % screens.length;
    loadScreen();
}

/* AUTO ROTATION */
function startRotation() {
    interval = setInterval(nextScreen, 15000);
}

/* RESET TIMER ON MANUAL CLICK */
function resetRotation() {
    clearInterval(interval);
    startRotation();
}

/* BUTTON HOOKS */
document.querySelectorAll('.btn').forEach(btn => {
    btn.addEventListener('click', resetRotation);
});

/* INIT */
loadScreen();
startRotation();

</script>

</body>
</html>