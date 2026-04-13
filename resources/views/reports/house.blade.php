@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">House Performance Report</h1>

    <div id="house-comparison" style="min-height: 350px; margin-bottom: 1rem;"></div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px;">
            <thead>
                <tr style="border-bottom: 1px solid #334155;">
                    <th style="text-align: left; padding: 10px 12px;">House</th>
                    <th style="text-align: right; padding: 10px 12px;">Year total</th>
                    <th style="text-align: right; padding: 10px 12px;">This term</th>
                    <th style="text-align: right; padding: 10px 12px;">Previous term</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($housePerformance as $house)
                    <tr style="border-bottom: 1px solid #334155;">
                        <td style="padding: 10px 12px; font-weight: 600;">{{ $house['house'] }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['year_total']) }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['term_total']) }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['last_term_total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const houses = @json($housePerformance);

            const names = houses.map(h => h.house);
            const thisTerm = houses.map(h => Number(h.this_term ?? h.term_total ?? 0));
            const previousTerm = houses.map(h => Number(h.previous_term ?? h.last_term_total ?? 0));

            const options = {
                chart: {
                    type: 'bar',
                    height: 350,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (!house) {
                                return;
                            }

                            if (typeof drillDown === 'function') {
                                drillDown({
                                    type: 'house_low',
                                    value: house
                                });
                            }
                        }
                    }
                },
                series: [
                    {
                        name: 'This Term',
                        data: thisTerm
                    },
                    {
                        name: 'Previous Term',
                        data: previousTerm
                    }
                ],
                xaxis: {
                    categories: names
                },
                title: {
                    text: 'House Performance (Term Comparison)'
                }
            };

            const chart = new ApexCharts(document.querySelector('#house-comparison'), options);
            chart.render();
        });
    </script>
@endpush
