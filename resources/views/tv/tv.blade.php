<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HouseHub TV</title>

<style>
body {
    margin: 0;
    background: black;
    overflow: hidden;
}

/* SCREENS */
.screen {
    position: absolute;
    width: 100vw;
    height: 100vh;
    border: none;

    opacity: 0;
    transform: translateX(5%);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.screen.active {
    opacity: 1;
    transform: translateX(0);
}

/* NEXT BUTTON (debug only) */
.next-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;

    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    padding: 12px 18px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
}

.next-btn:hover {
    background: rgba(255,255,255,0.2);
}
</style>
</head>

<body>

<!-- ALL TV SCREENS -->

<!-- Core -->
<iframe class="screen active" src="/tv/weather"></iframe>
<iframe class="screen" src="/tv/leaderboard"></iframe>
<iframe class="screen" src="/tv/top-students"></iframe>
<iframe class="screen" src="/tv/house-race"></iframe>
<iframe class="screen" src="/tv/teachers"></iframe>
<iframe class="screen" src="/tv/house-trends"></iframe>

<!-- Extended -->
<iframe class="screen" src="/tv/house-month"></iframe>
<iframe class="screen" src="/tv/house-year"></iframe>
<iframe class="screen" src="/tv/teachers-month"></iframe>
<iframe class="screen" src="/tv/house-momentum"></iframe>

<!-- NEXT BUTTON -->
<button class="next-btn" onclick="manualNext()">Next ▶</button>

<script>

const screens = document.querySelectorAll('.screen');

let index = 0;
let timer;

// ⏱ timing
const NORMAL = 12000;
const HERO = 20000;

// 🌟 hero screens
const hero = [
    0, // weather
    1  // leaderboard
];

// 🔁 ROTATE
function rotate() {

    screens[index].classList.remove('active');

    index = (index + 1) % screens.length;

    screens[index].classList.add('active');

    scheduleNext();
}

// ⏱ TIMER CONTROL
function scheduleNext() {
    clearTimeout(timer);

    const delay = hero.includes(index) ? HERO : NORMAL;

    timer = setTimeout(rotate, delay);
}

// 👉 MANUAL NEXT
function manualNext() {
    rotate();
}

// 👉 KEYBOARD CONTROL (for testing)
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') manualNext();
});

// START
scheduleNext();

</script>

</body>
</html>