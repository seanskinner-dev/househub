<!DOCTYPE html>
<html>
<head>
    <title>HouseHub</title>

    <style>
        body {
            margin: 0;
            font-family: Inter, system-ui;
            background: #0b0f19; /* 🔥 upgraded darker */
            color: white;
        }

        .container {
            padding: 20px;
            height: calc(100vh - 70px);
            overflow: hidden;
        }
    </style>
</head>

<body>

    @include('layouts.navbar')

    <div class="container">
        @yield('content')
    </div>

</body>
</html>