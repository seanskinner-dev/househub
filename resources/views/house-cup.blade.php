@extends('layouts.tv')

@section('content')

@php
    $winner = $houseTotals[0] ?? null;
@endphp

<div class="cup-container">

    @if($winner)

    <!-- 🏆 HERO -->
    <div class="hero" style="background: {{ $winner->colour_hex }}">

        <div class="hero-inner">

            <div class="trophy">🏆</div>

            <div class="title">
                HOUSE CUP WINNER
            </div>

            <div class="house">
                {{ strtoupper($winner->name) }}
            </div>

            <div class="points">
                {{ number_format($winner->points) }}
            </div>

        </div>

    </div>

    @endif


    <!-- 🥈 RANKINGS -->
    <div class="rankings">

        @foreach($houseTotals as $index => $house)
            @if($index > 0)

            <div class="rank-row">

                <div class="rank">#{{ $index + 1 }}</div>

                <div class="name" style="color: {{ $house->colour_hex }}">
                    {{ $house->name }}
                </div>

                <div class="points">
                    {{ number_format($house->points) }}
                </div>

            </div>

            @endif
        @endforeach

    </div>

</div>

@endsection


<style>

/* FULLSCREEN */
body {
    margin: 0;
    overflow: hidden;
    font-family: Inter, system-ui;
}

.cup-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
}

/* HERO */
.hero {
    flex: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.hero-inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.trophy {
    font-size: 80px;
}

.title {
    font-size: 24px;
    opacity: 0.8;
    letter-spacing: 2px;
}

.house {
    font-size: 80px;
    font-weight: 900;
    letter-spacing: -2px;
}

.points {
    font-size: 100px;
    font-weight: 900;
}

/* RANKINGS */
.rankings {
    flex: 1;
    background: #0f172a;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.rank-row {
    display: flex;
    justify-content: space-between;
    padding: 20px 60px;
    font-size: 32px;
    color: white;
}

.rank {
    opacity: 0.6;
}

.name {
    font-weight: 800;
}

.points {
    font-weight: 900;
}

</style>