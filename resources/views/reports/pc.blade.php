@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 52rem;">
        Interactive charts — click to drill down. Filters combine in the URL.
        @if ($filter || $selectedDate || $selectedHouse)
            <a href="{{ route('reports.pc') }}" style="color: #93c5fd; margin-left: 0.5rem;">Clear all</a>
        @endif
    </p>

    @if ($filter || $selectedDate || $selectedHouse)
        <p style="font-size: 1rem; margin-bottom: 2rem; opacity: 0.95;">
            @if ($filter)
                <span style="display: inline-block; margin-right: 12px; padding: 6px 12px; background: #334155; border-radius: 6px;">
                    Risk: <strong>{{ ucfirst($filter) }}</strong>
                </span>
            @endif
            @if ($selectedDate)
                <span style="display: inline-block; margin-right: 12px; padding: 6px 12px; background: #334155; border-radius: 6px;">
                    Date: <strong>{{ $selectedDate }}</strong>
                </span>
            @endif
            @if ($selectedHouse)
                <span style="display: inline-block; padding: 6px 12px; background: #334155; border-radius: 6px;">
                    House: <strong>{{ $selectedHouse }}</strong>
                </span>
            @endif
        </p>
    @endif

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Risk distribution</h2>
        <div style="max-width: 440px;">
            <div id="pc-risk-chart"></div>
        </div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Engagement trend (30 days)</h2>
        <p style="font-size: 0.95rem; opacity: 0.85; margin-bottom: 0.75rem;">Daily total points — click a point to select a date.</p>
        <div id="pc-trend-chart" style="min-height: 380px;"></div>
    </section>

    <section style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">House breakdown</h2>
        <p style="font-size: 0.95rem; opacity: 0.85; margin-bottom: 0.75rem;">Student counts for the current risk view — click a bar to filter by house.</p>
        <div id="pc-house-chart" style="min-height: 400px;"></div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    <script>
        (function () {
            var pcBase = @json(route('reports.pc'));

            function pcNavigate(patch) {
                var u = new URL(pcBase, window.location.origin);
                var p = new URLSearchParams(window.location.search);
                Object.keys(patch).forEach(function (k) {
                    var v = patch[k];
                    if (v === null || v === '') {
                        p.delete(k);
                    } else {
                        p.set(k, String(v));
                    }
                });
                var qs = p.toString();
                window.location.href = u.pathname + (qs ? '?' + qs : '');
            }

            var trendData = @json($trendData);
            var houseRisk = @json($houseRiskCounts);
            var trendCategories = trendData.map(function (d) { return d.date; });
            var trendSeries = trendData.map(function (d) { return d.points; });
            var houseCategories = houseRisk.map(function (r) { return r.house; });
            var houseSeries = houseRisk.map(function (r) { return r.count; });
            var selectedDate = @json($selectedDate);

            var commonFont = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };

            new ApexCharts(document.querySelector('#pc-risk-chart'), {
                series: [{{ $counts['high'] }}, {{ $counts['medium'] }}, {{ $counts['active'] }}],
                labels: ['High Risk', 'Medium Risk', 'Active'],
                chart: {
                    type: 'donut',
                    height: 380,
                    fontFamily: commonFont.fontFamily,
                    foreColor: commonFont.foreColor,
                    toolbar: { show: false },
                    events: {
                        dataPointSelection: function (event, chartContext, config) {
                            var keys = ['high', 'medium', 'active'];
                            var key = keys[config.dataPointIndex];
                            if (key) {
                                pcNavigate({ filter: key });
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
                                total: { show: true, label: 'Students', fontSize: '14px' }
                            }
                        }
                    }
                },
                colors: ['#b91c1c', '#d97706', '#15803d'],
                legend: { position: 'bottom', fontSize: '15px' },
                dataLabels: { enabled: true, style: { fontSize: '13px' } },
                tooltip: {
                    y: { formatter: function (val) { return val + ' students'; } }
                }
            }).render();

            var lineAnnotations = { xaxis: [] };
            if (selectedDate && trendCategories.indexOf(selectedDate) !== -1) {
                lineAnnotations.xaxis.push({
                    x: selectedDate,
                    borderColor: '#93c5fd',
                    label: {
                        borderColor: '#93c5fd',
                        style: { color: '#0f172a', background: '#93c5fd' },
                        text: selectedDate
                    }
                });
            }

            new ApexCharts(document.querySelector('#pc-trend-chart'), {
                series: [{ name: 'Points', data: trendSeries }],
                chart: {
                    type: 'line',
                    height: 400,
                    fontFamily: commonFont.fontFamily,
                    foreColor: commonFont.foreColor,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    selection: { enabled: true },
                    events: {
                        markerClick: function (event, chartContext, config) {
                            var idx = config.dataPointIndex;
                            if (trendCategories[idx] !== undefined) {
                                pcNavigate({ date: trendCategories[idx] });
                            }
                        },
                        dataPointSelection: function (event, chartContext, config) {
                            var idx = config.dataPointIndex;
                            if (trendCategories[idx] !== undefined) {
                                pcNavigate({ date: trendCategories[idx] });
                            }
                        },
                        click: function (event, chartContext, config) {
                            if (config && config.dataPointIndex >= 0 && trendCategories[config.dataPointIndex]) {
                                pcNavigate({ date: trendCategories[config.dataPointIndex] });
                            }
                        }
                    }
                },
                stroke: { curve: 'smooth', width: 3 },
                markers: { size: 5, hover: { size: 8 } },
                xaxis: {
                    categories: trendCategories,
                    labels: { rotate: -45, rotateAlways: trendCategories.length > 14, style: { fontSize: '12px' } }
                },
                yaxis: { labels: { style: { fontSize: '13px' } } },
                grid: { borderColor: '#334155' },
                annotations: lineAnnotations,
                tooltip: {
                    x: { format: 'yyyy-MM-dd' },
                    y: { formatter: function (val) { return val + ' pts'; } }
                }
            }).render();

            new ApexCharts(document.querySelector('#pc-house-chart'), {
                series: [{ name: 'Students', data: houseSeries }],
                chart: {
                    type: 'bar',
                    height: 420,
                    fontFamily: commonFont.fontFamily,
                    foreColor: commonFont.foreColor,
                    toolbar: { show: false },
                    events: {
                        dataPointSelection: function (event, chartContext, config) {
                            var name = houseCategories[config.dataPointIndex];
                            if (name) {
                                pcNavigate({ house: name });
                            }
                        }
                    }
                },
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                colors: ['#3b82f6'],
                xaxis: {
                    categories: houseCategories,
                    labels: { style: { fontSize: '13px' } }
                },
                yaxis: {
                    labels: { style: { fontSize: '13px' } },
                    tickAmount: 6,
                    min: 0
                },
                grid: { borderColor: '#334155' },
                dataLabels: { enabled: houseCategories.length <= 16, style: { fontSize: '12px' } },
                tooltip: {
                    y: { formatter: function (val) { return val + ' students'; } }
                }
            }).render();
        })();
    </script>
@endpush
