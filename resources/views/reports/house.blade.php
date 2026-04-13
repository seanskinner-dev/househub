<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            House Performance Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">House</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Year total</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">This term</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold text-gray-700">Previous term</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($housePerformance as $row)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $row['house'] }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums">{{ number_format($row['year_total']) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums">{{ number_format($row['term_total']) }}</td>
                                    <td class="px-4 py-2 text-right tabular-nums">{{ number_format($row['last_term_total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
