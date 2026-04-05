<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HouseHub</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #0f172a;
            color: white;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        }
    </style>
</head>
<body>

    <!-- ✅ ONLY THIS (NO $slot ANYWHERE) -->
    @yield('content')

    @stack('scripts')

</body>
</html>