<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HouseHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            color: white;
        }

        /* NAVBAR (hh- prefix avoids clashing with Bootstrap .navbar) */
        .hh-navbar {
            width: 100%;
            background: #1e293b;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            box-sizing: border-box;
        }

        .nav-left {
            font-size: 18px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-right > a {
            color: white;
            text-decoration: none;
        }

        .nav-right > a:hover {
            text-decoration: underline;
        }

        .nav-right > a.active {
            text-decoration: underline;
            border-bottom: 2px solid #fff;
            padding-bottom: 2px;
        }

        .nav-right .dropdown-toggle {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 0;
        }

        .nav-right .dropdown-toggle:hover,
        .nav-right .dropdown-toggle:focus {
            color: #fff;
            text-decoration: underline;
        }

        .nav-right .dropdown-toggle.active {
            text-decoration: underline;
            border-bottom: 2px solid #fff;
            padding-bottom: 2px;
        }

        .nav-right .dropdown-toggle::after {
            margin-left: 0.35em;
            vertical-align: 0.15em;
        }

        .nav-right .dropdown-menu {
            background: #1e293b;
            border: 1px solid #334155;
            margin-top: 0.5rem;
            padding: 0.35rem 0;
        }

        .nav-right .dropdown-item {
            color: #f1f5f9;
            font-weight: normal;
            padding: 0.45rem 1rem;
        }

        .nav-right .dropdown-item:hover,
        .nav-right .dropdown-item:focus {
            background: #334155;
            color: #fff;
        }

        .nav-right .dropdown-item.active,
        .nav-right .dropdown-item:active {
            background: #475569;
            color: #fff;
        }

        /* PAGE CONTENT */
        .page-content {
            padding: 20px;
        }

        .page-content .card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: none;
            border-radius: 12px;
            color: #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
        }

        .page-content .card:hover {
            transform: translateY(-2px);
        }

        .page-content .card h5 {
            color: #f8fafc;
            font-weight: 600;
        }

        .page-content .card p,
        .page-content .card .text-muted,
        .page-content .card .small {
            color: #94a3b8 !important;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="hh-navbar">
        <div class="nav-left">🏫 HouseHub</div>

        <div class="nav-right">
            <a href="/points">Points</a>

            <div class="dropdown">
                <a class="dropdown-toggle {{ request()->routeIs('reports.pc', 'reports.leadership', 'reports.teachers', 'reports.house', 'reports.houses') ? 'active' : '' }}"
                   href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Reports
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="reportsDropdown">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.house') ? 'active' : '' }}"
                           href="{{ route('reports.house') }}">House performance</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.pc') ? 'active' : '' }}"
                           href="{{ route('reports.pc') }}">Pastoral Insights</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.leadership') ? 'active' : '' }}"
                           href="{{ route('reports.leadership') }}">Leadership Overview</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.teachers') ? 'active' : '' }}"
                           href="{{ route('reports.teachers') }}">Staff Engagement</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.houses') ? 'active' : '' }}"
                           href="{{ route('reports.houses') }}">House Performance</a>
                    </li>
                </ul>
            </div>

            <a href="/tv">TV</a>
            <a href="/admin">Admin</a>
        </div>
    </div>

    {{-- @yield for @extends; $slot for <x-app-layout> (both may be empty; only one is used) --}}
    <div class="page-content">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts failed to load');
        }
        window.Apex = {
            chart: {
                background: 'transparent'
            }
        };
    </script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
