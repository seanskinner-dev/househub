@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">House Performance Report</h1>

    <div id="house-comparison" style="min-height: 300px;"></div>
    <div id="house-momentum" class="mt-4" style="min-height: 300px;"></div>
    <div id="house-contribution" class="mt-4" style="min-height: 300px;"></div>
    <div id="house-risk" class="mt-4" style="min-height: 300px;"></div>

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
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const data = @json($housePerformance);
            console.log('Chart data:', data);

            const names = data.map(h => h.house);
            const thisTerm = data.map(h => Number(h.this_term ?? h.term_total ?? 0));
            const previousTerm = data.map(h => Number(h.previous_term ?? h.last_term_total ?? 0));

            function drillDown(payload) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch('/reports/drilldown', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload || {})
                }).catch(function () {});
            }

            //-----------------------------------
            // CLEAR OLD INSTANCES
            //-----------------------------------
            document.querySelectorAll('#house-comparison, #house-momentum, #house-contribution, #house-risk')
                .forEach(el => el.innerHTML = '');

            try {
                //-----------------------------------
                // 1. TERM COMPARISON
                //-----------------------------------
                new ApexCharts(document.querySelector("#house-comparison"), {
                chart: {
                    type: 'bar',
                    height: 300,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'house_low', value: house });
                            }
                        }
                    }
                },
                series: [
                    { name: 'This Term', data: thisTerm },
                    { name: 'Previous Term', data: previousTerm }
                ],
                xaxis: { categories: names },
                title: { text: 'Term Comparison' }
                }).render();

                //-----------------------------------
                // 2. MOMENTUM
                //-----------------------------------
                new ApexCharts(document.querySelector("#house-momentum"), {
                chart: {
                    type: 'line',
                    height: 300,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'house_low', value: house });
                            }
                        }
                    }
                },
                series: [{ name: 'Momentum', data: thisTerm }],
                xaxis: { categories: names },
                title: { text: 'Momentum' }
                }).render();

                //-----------------------------------
                // 3. CONTRIBUTION
                //-----------------------------------
                const contribution = thisTerm.map(v => Math.max(1, Math.floor(v / 10)));

                new ApexCharts(document.querySelector("#house-contribution"), {
                chart: {
                    type: 'radar',
                    height: 300,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'house_low', value: house });
                            }
                        }
                    }
                },
                series: [{
                    name: 'Contribution',
                    data: contribution
                }],
                labels: names,
                title: { text: 'Contribution Spread' }
                }).render();

                //-----------------------------------
                // 4. RISK
                //-----------------------------------
                const risk = thisTerm.map(v => Math.floor(100 / (v + 1)));

                new ApexCharts(document.querySelector("#house-risk"), {
                chart: {
                    type: 'bar',
                    height: 300,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'house_low', value: house });
                            }
                        }
                    }
                },
                series: [{ name: 'Risk', data: risk }],
                xaxis: { categories: names },
                title: { text: 'Underperformance' }
                }).render();
            } catch (e) {
                console.error('Chart render failed:', e);
            }
        });
    </script>
@endpush
