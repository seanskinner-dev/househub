<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Hub - Award Points</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-100 p-8">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-slate-800 mb-6">House Hub Dashboard</h1>

        @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold mb-4">House Points</h2>
                <form action="{{ route('points.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">House</label>
                        <select name="house_id" class="w-full border p-2 rounded" required>
                            @foreach($houses as $house)
                                <option value="{{ $house->id }}">{{ $house->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Amount</label>
                        <input type="number" name="amount" value="1" class="w-full border p-2 rounded">
                    </div>
                    <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded font-bold hover:bg-slate-700">Submit Points</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold mb-4 text-amber-600">Official Commendation</h2>
                <form action="{{ route('commendations.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Student</label>
                        <select name="student_id" class="w-full border p-2 rounded">
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="amount" value="20">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Note</label>
                        <textarea name="description" class="w-full border p-2 rounded" placeholder="Why are they receiving this?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-amber-500 text-white py-2 rounded font-bold hover:bg-amber-600">Issue Commendation (+20)</button>
                </form>
            </div>

        </div>

        <div class="mt-12 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="p-4 font-semibold">Student</th>
                        <th class="p-4 font-semibold">House</th>
                        <th class="p-4 font-semibold text-center">Points</th>
                        <th class="p-4 font-semibold text-right">Quick Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students->take(20) as $student)
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                        <td class="p-4">{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-bold text-white" style="background-color: {{ $student->colour_hex }}">
                                {{ $student->house_name }}
                            </span>
                        </td>
                        <td class="p-4 text-center font-mono font-bold" id="house-points-{{ $student->id }}">{{ $student->house_points }}</td>
                        <td class="p-4 text-right space-x-2">
                            <button onclick="quickAdjust({{ $student->id }}, -1)" class="px-3 py-1 bg-red-100 text-red-600 rounded hover:bg-red-200">-1</button>
                            <button onclick="quickAdjust({{ $student->id }}, 1)" class="px-3 py-1 bg-green-100 text-green-600 rounded hover:bg-green-200">+1</button>
                            <button onclick="quickAdjust({{ $student->id }}, 10)" class="px-3 py-1 bg-amber-100 text-amber-600 rounded hover:bg-amber-200">⭐</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function quickAdjust(studentId, amount) {
            fetch('{{ route("points.quick-adjust") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    student_id: studentId,
                    amount: amount,
                    category: 'Quick Action'
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('house-points-' + studentId).innerText = data.new_total;
                }
            });
        }
    </script>
</body>
</html>