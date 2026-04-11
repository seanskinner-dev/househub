<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Great Hall - House Cup</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=Eagle+Lake&family=Crimson+Pro:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #0f172a; 
            background-image: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            font-family: 'Crimson Pro', serif;
        }
        .parchment {
            background-color: #f3e5ab;
            background-image: url('https://www.transparenttextures.com/patterns/beige-paper.png');
            color: #262626;
        }
        .magic-title { font-family: 'Cinzel Decorative', cursive; }
        .house-font { font-family: 'Eagle Lake', cursive; }
    </style>
</head>
<body class="p-4 md:p-12 min-h-screen text-slate-200">

    <div class="max-w-6xl mx-auto">
        <header class="mb-16 text-center">
            <h1 class="magic-title text-5xl md:text-7xl text-amber-500 mb-4 tracking-widest drop-shadow-[0_5px_5px_rgba(0,0,0,0.5)]">
                The House Cup
            </h1>
            <div class="h-1 w-64 bg-gradient-to-r from-transparent via-amber-500 to-transparent mx-auto"></div>
            <p class="mt-4 italic text-xl text-slate-400">Term 1 Standings</p>
        </header>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-16">
            @foreach($houseTotals as $house)
                <div class="flex flex-col items-center">
                    <div class="relative w-full h-48 bg-zinc-900/80 border-2 rounded-b-full overflow-hidden shadow-[0_0_20px_rgba(0,0,0,0.5)]" 
                         style="border-color: {{ $house->colour_hex }}">
                        <div class="absolute bottom-0 w-full transition-all duration-1000 ease-out" 
                             style="background-color: {{ $house->colour_hex }}; height: 60%; opacity: 0.8; box-shadow: inset 0 0 20px rgba(0,0,0,0.5);">
                        </div>
                    </div>
                    <h3 class="house-font mt-4 text-xl" style="color: {{ $house->colour_hex }}">{{ $house->house_name }}</h3>
                    <p class="text-3xl font-bold">{{ number_format($house->total_points) }}</p>
                </div>
            @endforeach
        </div>

        <div class="parchment rounded-sm shadow-[0_20px_50px_rgba(0,0,0,0.5)] overflow-hidden border-8 border-[#3d2b1f]">
            <div class="p-8 border-b-2 border-[#3d2b1f]/20 text-center">
                <h2 class="magic-title text-3xl uppercase tracking-tighter">Student Points Ledger</h2>
            </div>
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
                                {{ $student->house_points }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>