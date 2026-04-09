@extends('layouts.app')

@section('content')
<style>
    body { background:#0b0f1a; font-family: 'Segoe UI', sans-serif; color:white; }

    .wrap { max-width:1100px; margin:20px auto; }

    .header {
        font-size:5vh;
        font-weight:bold;
        text-align:center;
        margin-bottom:2vh;
    }

    /* STUDENT GRID */
    .student-grid {
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap:1.5vh;
    }

    /* CARD */
    .card {
        padding:20px;
        border-radius:12px;
        background:rgba(255,255,255,0.05);
        display:flex;
        justify-content:space-between;
        align-items:center;
    }

    /* NAME */
    .name {
        font-weight:bold;
        font-size:2.2vh;
    }

    /* META */
    .meta {
        font-size:1.6vh;
        opacity:0.7;
    }

    /* POINTS */
    .points {
        font-size:2.5vh;
        font-weight:bold;
    }
</style>

<div class="wrap">

    <div class="header">Top Students</div>

    <div class="student-grid">

        @foreach($students as $student)
        <div class="card">

            <div>
                <div class="name">
                    {{ $student->first_name }} {{ $student->last_name }}
                </div>

                <div class="meta">
                    Y{{ $student->year_level }} • {{ $student->house_name }}
                </div>
            </div>

            <div class="points">
                {{ $student->points }} pts
            </div>

        </div>
        @endforeach

    </div>

</div>

@endsection