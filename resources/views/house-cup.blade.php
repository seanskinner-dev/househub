@extends('layouts.app') <!-- Extending the main layout file -->

@section('title', 'The House Cup - Term 1 Standings') <!-- Title for this page -->

@section('content')
    <header class="text-center">
        <h1 class="magic-title">The House Cup</h1>
        <p class="text-xl">Term 1 Standings</p>
    </header>

    <div class="parchment rounded-sm shadow-lg overflow-hidden border-8 border-[#3d2b1f]">
        <div class="p-8 border-b-2 border-[#3d2b1f]/20 text-center">
            <h2 class="magic-title text-3xl uppercase tracking-tighter">House Totals</h2>
        </div>

        <!-- Display House Totals -->
        <div class="flex flex-wrap justify-between">
            @foreach($houseTotals as $house)
                <div class="house-card" style="border-color: {{ $house->colour_hex }};">
                    <div class="house-banner" style="background-color: {{ $house->colour_hex }};"></div>
                    <h3 class="house-name" style="color: {{ $house->colour_hex }}">{{ $house->house_name }}</h3>
                    <p class="house-points">{{ number_format($house->total_points) }} Points</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="parchment rounded-sm shadow-lg overflow-hidden border-8 border-[#3d2b1f] mt-8">
        <div class="p-8 border-b-2 border-[#3d2b1f]/20 text-center">
            <h2 class="magic-title text-3xl uppercase tracking-tighter">Student Points Ledger</h2>
        </div>

        <!-- Display Students -->
        <table class="w-full text-left">
            <thead>
                <tr class="border-b-2 border-[#3d2b1f]/10 italic text-[#3d2b1f]/60">
                    <th class="p-6">Rank</th>
                    <th class="p-6">Name</th>
                    <th class="p-6">House</th>
                    <th class="p-6 text-right">Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    <tr class="border-b border-[#3d2b1f]/5 hover:bg-[#3d2b1f]/5 transition-all">
                        <td class="p-6 font-bold">#{{ $index + 1 }}</td>
                        <td class="p-6">
                            <span class="text-xl font-bold block">{{ $student->first_name }} {{ $student->last_name }}</span>
                            <span class="text-sm italic opacity-70">Year {{ $student->year_level }} Student</span>
                        </td>
                        <td class="p-6">
                            <span class="house-font text-lg" style="color: {{ $student->colour_hex }}">
                                {{ $student->house_name }}
                            </span>
                        </td>
                        <td class="p-6 text-right text-2xl font-bold">
                            {{ $student->points }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection