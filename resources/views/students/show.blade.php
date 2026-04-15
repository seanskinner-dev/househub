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
            max-width:700px;
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

        .section {
            margin-top:30px;
        }

        .stat-box {
            display:flex;
            gap:20px;
            margin-top:15px;
        }

        .stat {
            background:#2b2f36;
            padding:15px 20px;
            border-radius:10px;
            text-align:center;
            min-width:100px;
        }

        .stat strong {
            display:block;
            font-size:20px;
        }

        .award {
            background:#2b2f36;
            padding:15px;
            border-radius:10px;
            margin-bottom:10px;
        }

        .award-title {
            font-weight:bold;
        }

        .award-date {
            font-size:12px;
            color:#aaa;
        }

        .btn {
            display:inline-block;
            margin-top:10px;
            padding:6px 12px;
            background:#60a5fa;
            color:white;
            border-radius:6px;
            text-decoration:none;
            font-size:12px;
        }
    </style>
</head>

<body>

<div class="card">
    <h1>{{ $student->first_name }} {{ $student->last_name }}</h1>

    <div class="meta">
        {{ $student->house_name ?? 'No house assigned' }}<br>
        {{ $student->house_points }} points
    </div>

    <!-- ===================== -->
    <!-- STATS -->
    <!-- ===================== -->
    <div class="section">
        <h3>Stats</h3>

        <div class="stat-box">
            <div class="stat">
                <strong>{{ $student->house_points }}</strong>
                Points
            </div>

            <div class="stat">
                <strong>{{ $awardCount }}</strong>
                Awards
            </div>

            <div class="stat">
                <strong>{{ $commendationCount }}</strong>
                Commendations
            </div>
        </div>
    </div>

    <!-- ===================== -->
    <!-- AWARDS -->
    <!-- ===================== -->
    <div class="section">
        <h3>Awards</h3>

        @if($awards->count())
            @foreach($awards as $award)
                <div class="award">
                    <div class="award-title">
                        {{ $award->name ?? $award->title ?? 'Award' }}
                    </div>

                    <div>
                        {{ $award->description }}
                    </div>

                    <div class="award-date">
                        {{ \Carbon\Carbon::parse($award->awarded_at ?? $award->created_at)->format('d M Y') }}
                    </div>

                    <a href="/certificate/{{ $award->id }}" class="btn">View Certificate</a>
                </div>
            @endforeach
        @else
            <div class="meta">No awards yet</div>
        @endif
    </div>

    <!-- ===================== -->
    <!-- COMMENDATIONS -->
    <!-- ===================== -->
    <div class="section">
        <h3>Commendations</h3>

        @if($commendations->count())
            @foreach($commendations as $c)
                <div class="award">
                    <div>
                        {{ $c->description }}
                    </div>

                    <div class="award-date">
                        {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}
                    </div>
                </div>
            @endforeach
        @else
            <div class="meta">No commendations yet</div>
        @endif
    </div>

    <br>

    <a href="/points">← Back to Points</a>
</div>

</body>
</html>