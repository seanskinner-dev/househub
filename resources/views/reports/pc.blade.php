@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 1.25rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1rem; max-width: 48rem;">
        Risk distribution (last 30 days). Click a segment to filter the table below.
        @if ($filter)
            <a href="{{ route('reports.pc') }}" style="color: #93c5fd; margin-left: 0.5rem;">Clear filter</a>
        @endif
    </p>

    <div style="max-width: 420px; margin-bottom: 2rem;">
        <div id="pc-risk-chart"></div>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 1.125rem; background: #1e293b; border-radius: 8px;">
            <thead>
                <tr style="border-bottom: 2px solid #334155;">
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">Name</th>
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">House</th>
                    <th style="text-align: right; padding: 16px 18px; font-size: 1.05rem;">Points (30 days)</th>
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">Last activity</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $row)
                    @php
                        $zeroPoints = ($row['points_last_30_days'] ?? 0) === 0;
                    @endphp
                    <tr style="border-bottom: 1px solid #334155; {{ $zeroPoints ? 'background: rgba(127, 29, 29, 0.45);' : '' }}">
                        <td style="padding: 16px 18px; font-weight: 600;">{{ $row['name'] }}</td>
                        <td style="padding: 16px 18px;">{{ $row['house'] }}</td>
                        <td style="padding: 16px 18px; text-align: right; font-variant-numeric: tabular-nums;">
                            {{ number_format($row['points_last_30_days']) }}
                        </td>
                        <td style="padding: 16px 18px;">
                            @if ($row['last_activity'] === null)
                                <span style="opacity: 0.85;">—</span>
                            @else
                                {{ $row['last_activity'] }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 24px 18px; text-align: center; opacity: 0.9;">
                            No students in this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    <script>
        (function () {
            var options = {
                series: [{{ $counts['high'] }}, {{ $counts['medium'] }}, {{ $counts['active'] }}],
                labels: ['High Risk', 'Medium Risk', 'Active'],
                chart: {
                    type: 'donut',
                    height: 360,
                    fontFamily: 'Arial, sans-serif',
                    foreColor: '#e2e8f0',
                    events: {
                        dataPointSelection: function (event, chartContext, config) {
                            var keys = ['high', 'medium', 'active'];
                            var key = keys[config.dataPointIndex];
                            if (key) {
                                window.location.href = @json(route('reports.pc')) + '?filter=' + encodeURIComponent(key);
                            }
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Students',
                                    fontSize: '14px'
                                }
                            }
                        }
                    }
                },
                colors: ['#b91c1c', '#d97706', '#15803d'],
                legend: {
                    position: 'bottom',
                    fontSize: '14px'
                },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '13px' }
                },
                tooltip: {
                    y: {
                        formatter: function (val) { return val + ' students'; }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector('#pc-risk-chart'), options);
            chart.render();
        })();
    </script>
@endpush
