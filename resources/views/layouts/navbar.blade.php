<nav class="navbar">
    <div class="container">
        <ul class="nav-list">
            <li><a href="/">Home</a></li>
            <li><a href="/house-cup">House Cup</a></li>
            <li><a href="/about">About</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </div>
</nav>
<style>
    .navbar {
        background-color: #333;
        color: white;
        padding: 15px;
    }

    .nav-list {
        list-style-type: none;
        display: flex;
        justify-content: space-around;
    }

    .nav-list li {
        margin: 0 10px;
    }

    .nav-list a {
        color: white;
        text-decoration: none;
        font-size: 1.2rem;
    }

    .nav-list a:hover {
        text-decoration: underline;
    }
</style>