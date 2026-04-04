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
            background:#3a3f47;
            padding:30px;
            border-radius:16px;
            max-width:500px;
        }

        h1 {
            margin-bottom:10px;
        }

        .meta {
            color:#ccc;
        }

        a {
            color:#60a5fa;
        }
    </style>
</head>

<body>

<div class="card">
    <h1>{{ $student->first_name }} {{ $student->last_name }}</h1>

    <div class="meta">
        {{ $student->house_name }}<br>
        {{ $student->house_points }} points
    </div>

    <br>

    <a href="/points">← Back to Points</a>
</div>

</body>
</html>
