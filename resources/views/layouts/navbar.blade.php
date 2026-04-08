<style>
.navbar {
    background:#1f2329;
    padding:14px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.nav-left {
    display:flex;
    gap:20px;
}

.nav-link {
    color:white;
    text-decoration:none;
    font-weight:600;
}

.nav-link:hover {
    opacity:0.7;
}

.nav-right {
    color:#9ca3af;
    font-size:14px;
}
</style>

<div class="navbar">

    <div class="nav-left">
        <a href="/points" class="nav-link">Points</a>
        <a href="/dashboard" class="nav-link">Dashboard</a>
        <a href="/tv" class="nav-link">TV</a>
        <a href="/house-cup" class="nav-link">House Cup</a>
    </div>

    <div class="nav-right">
        {{ auth()->user()->name ?? 'User' }}
    </div>

</div>