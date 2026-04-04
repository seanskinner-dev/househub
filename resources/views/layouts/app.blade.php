<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <a class="navbar-brand" href="{{ route('points.index') }}">
            HouseHub
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('points.index') }}">
                        Award Points
                    </a>
                </li>

                {{-- Leaderboard removed for now to stop crash --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" href="{{ route('leaderboard') }}">
                        Leaderboard
                    </a>
                </li> --}}

            </ul>
        </div>

    </div>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>