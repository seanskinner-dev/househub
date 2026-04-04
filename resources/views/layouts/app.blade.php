<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Hub - Sydney</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #eef2f7; font-family: 'Inter', sans-serif; }
        .nav-sidebar { width: 250px; background: #fff; height: 100vh; position: fixed; border-right: 1px solid #ddd; }
        .main-content { margin-left: 250px; padding: 20px; }
    </style>
</head>
<body>
    <div class="nav-sidebar">
        <div style="padding:20px; font-weight:bold; border-bottom:1px solid #eee;">🏆 HOUSE HUB</div>
        <ul style="list-style:none; padding:20px;">
            <li style="margin-bottom:15px;"><a href="{{ route('leaderboard') }}" style="text-decoration:none; color:#333;">Leaderboard</a></li>
            <li style="margin-bottom:15px;"><a href="{{ route('points.create') }}" style="text-decoration:none; color:#333; font-weight:bold;">Award Points</a></li>
        </ul>
    </div>

    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>