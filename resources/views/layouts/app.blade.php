<!DOCTYPE html>
<html>
<head>
    <title>HouseHub</title>

    <style>
        body {
            margin:0;
            font-family: Inter, system-ui;
            background:#2b2f36;
        }

        .container {
            padding:20px;
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