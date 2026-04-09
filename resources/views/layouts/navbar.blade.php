<style>
.navbar {
    background: #020617;
    padding: 14px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;

    border-bottom: 1px solid rgba(255,255,255,0.05);
    box-shadow: 0 0 25px rgba(0,0,0,0.8);
}

/* LEFT SIDE */
.nav-left {
    display: flex;
    gap: 26px;
    align-items: center;
}

/* LINKS */
.nav-link {
    position: relative;
    color: #94a3b8;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    padding-bottom: 4px;
    transition: all 0.25s ease;
}

/* HOVER */
.nav-link:hover {
    color: #ffffff;
}

/* 🔥 UNDERLINE ANIMATION */
.nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0%;
    height: 2px;
    background: white;
    transition: width 0.25s ease;
}

.nav-link:hover::after {
    width: 100%;
}

/* 🔥 ACTIVE LINK */
.nav-link.active {
    color: #ffffff;
}

.nav-link.active::after {
    width: 100%;
}

/* DIVIDER */
.nav-divider {
    opacity: 0.2;
}

/* RIGHT SIDE */
.nav-right {
    color: #9ca3af;
    font-size: 13px;
    letter-spacing: 0.5px;
}

/* 🔥 TV MODE BUTTON (SUBTLE) */
.tv-mode {
    margin-left: 10px;
    padding: 6px 12px;
    border-radius: 6px;
    background: rgba(255,255,255,0.05);
    color: #cbd5f5;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.tv-mode:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}
</style>

<div class="navbar">

    <div class="nav-left">

        <!-- CORE -->
        <a href="/points" class="nav-link {{ request()->is('points') ? 'active' : '' }}">Points</a>
        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>

        <span class="nav-divider">|</span>

        <!-- TV -->
        <a href="/tv" class="nav-link {{ request()->is('tv') ? 'active' : '' }}">TV</a>
        <a href="/tv/house-race" class="nav-link {{ request()->is('tv/house-race') ? 'active' : '' }}">Race</a>
        <a href="/tv/top-students" class="nav-link {{ request()->is('tv/top-students') ? 'active' : '' }}">Top Students</a>
        <a href="/tv/teachers" class="nav-link {{ request()->is('tv/teachers') ? 'active' : '' }}">Teachers</a>

        <span class="nav-divider">|</span>

        <!-- OTHER -->
        <a href="/house-cup" class="nav-link {{ request()->is('house-cup') ? 'active' : '' }}">House Cup</a>

        <!-- 🔥 TV MODE QUICK ACCESS -->
        <a href="/tv/house-race" class="tv-mode">TV Mode</a>

    </div>

    <div class="nav-right">
        {{ auth()->user()->name ?? 'User' }}
    </div>

</div>