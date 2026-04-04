<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>

    <style>
        body {
            font-family: Inter, system-ui;
            background:#2b2f36;
            color:white;
            padding:40px;
        }

        .card {
            background:#1f2329;
            padding:30px;
            border-radius:12px;
            max-width:600px;
            margin:auto;
        }

        h1 {
            margin-bottom:20px;
        }

        .meta {
            margin-bottom:10px;
        }

        a {
            color:#22c55e;
            text-decoration:none;
        }

        a:hover {
            text-decoration:underline;
        }
    </style>
</head>

<body>

<div class="card">

<h1>{{ $student->first_name }} {{ $student->last_name }}</h1>

<div class="meta"><strong>House:</strong> {{ $student->house_name }}</div>
<div class="meta"><strong>Points:</strong> {{ $student->house_points }}</div>
<div class="meta"><strong>Year:</strong> {{ $student->year_level ?? 'N/A' }}</div>

<br>

<a href="{{ route('points.index') }}">← Back to Award Points</a>

</div>

</body>
</html>