@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">House Performance Report</h1>
    <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 56rem;">
        House-focused insights emphasizing low or weakening outcomes. Click any chart element to drill into problem areas.
    </p>

    <section style="margin-bottom: 2.25rem;">
        <div id="house-rank" style="min-height: 380px;"></div>
    </section>

    <section style="margin-bottom: 2.25rem;">
        <div id="house-contribution" style="min-height: 380px;"></div>
    </section>

    <section style="margin-bottom: 2.25rem;">
        <div id="house-risk" style="min-height: 360px;"></div>
    </section>

    <section style="margin-bottom: 2rem;">
        <div id="house-momentum" style="min-height: 420px;"></div>
    </section>

    <div id="hr-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:1000;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#1e293b;color:#f1f5f9;max-width:920px;width:100%;max-height:86vh;overflow:auto;border-radius:10px;box-shadow:0 20px 50px rgba(0,0,0,0.5);">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #334155;">
                <h3 id="hr-modal-title" style="margin:0;font-size:1.1rem;">Details</h3>
                <button id="hr-modal-close" type="button" style="background:transparent;border:none;color:#fff;font-size:1.4rem;cursor:pointer;" aria-label="Close">&times;</button>
            </div>
            <div style="padding:16px 18px;">
                <p id="hr-empty" style="margin:0;opacity:0.9;display:none;">No rows.</p>
                <div id="hr-wrap" style="display:none;overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                        <thead><tr id="hr-thead"></tr></thead>
                        <tbody id="hr-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    <script>
        (function () {
            var dataUrl = @json(route('reports.data'));
            var drillUrl = @json(route('reports.drilldown'));
            var charts = { rank: null, contribution: null, risk: null, momentum: null };

            function chartDataSeries(rawSeries) {
                if (Array.isArray(rawSeries) && rawSeries.length && typeof rawSeries[0] === 'object' && rawSeries[0] && Array.isArray(rawSeries[0].data)) {
                    return rawSeries[0].data.map(function (v) { return Number(v) || 0; });
                }
                return (rawSeries || []).map(function (v) { return Number(v) || 0; });
            }

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function drillDown(payload) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch(drillUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                })
                    .then(function (res) { return res.json(); })
                    .then(renderDrillDownModal)
                    .catch(function () {});
            }

            function renderDrillDownModal(data) {
                var rows = data.rows || [];
                document.getElementById('hr-modal-title').textContent = data.title || 'Details';
                var empty = document.getElementById('hr-empty');
                var wrap = document.getElementById('hr-wrap');
                var thead = document.getElementById('hr-thead');
                var tbody = document.getElementById('hr-tbody');
                if (!rows.length) {
                    empty.style.display = 'block';
                    wrap.style.display = 'none';
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
                } else {
                    empty.style.display = 'none';
                    wrap.style.display = 'block';
                    var keys = Object.keys(rows[0]);
                    thead.innerHTML = keys.map(function (k) {
                        return '<th style="text-align:left;padding:8px 10px;border-bottom:2px solid #334155;">' + escapeHtml(k) + '</th>';
                    }).join('');
                    tbody.innerHTML = rows.map(function (r) {
                        return '<tr style="border-bottom:1px solid #334155;">' + keys.map(function (k) {
                            var v = r[k];
                            return '<td style="padding:8px 10px;">' + escapeHtml(v == null ? '' : String(v)) + '</td>';
                        }).join('') + '</tr>';
                    }).join('');
                }
                document.getElementById('hr-modal-backdrop').style.display = 'flex';
            }

            function destroyCharts() {
                Object.keys(charts).forEach(function (k) {
                    if (charts[k]) {
                        charts[k].destroy();
                        charts[k] = null;
                    }
                });
            }

            function renderRank(data) {
                var houses = (data.house_breakdown && data.house_breakdown.categories) ? data.house_breakdown.categories : [];
                var values = chartDataSeries(data.house_breakdown ? data.house_breakdown.series : []);
                var sorted = houses.map(function (h, i) {
                    return { name: h, value: values[i] || 0 };
                }).sort(function (a, b) {
                    return b.value - a.value;
                });

                charts.rank = new ApexCharts(document.querySelector('#house-rank'), {
                    chart: {
                        type: 'bar',
                        height: 380,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                var row = sorted[config.dataPointIndex];
                                if (row) {
                                    drillDown({ type: 'house_low', value: row.name });
                                }
                            }
                        }
                    },
                    series: [{ name: 'Weekday points', data: sorted.map(function (h) { return h.value; }) }],
                    xaxis: { categories: sorted.map(function (h) { return h.name; }) },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                    colors: ['#0ea5e9'],
                    title: { text: 'Rank Shift Pressure (click house for low-activity students)' },
                    tooltip: { theme: 'dark' }
                });
                charts.rank.render();
            }

            function renderContribution(data) {
                var houses = (data.house_breakdown && data.house_breakdown.categories) ? data.house_breakdown.categories : [];
                var source = chartDataSeries(data.house_breakdown ? data.house_breakdown.series : []);
                var values = source.map(function (v) { return Math.max(0, Math.floor(v / 10)); });

                charts.contribution = new ApexCharts(document.querySelector('#house-contribution'), {
                    chart: {
                        type: 'radar',
                        height: 380,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                var house = houses[config.dataPointIndex];
                                if (house) {
                                    drillDown({ type: 'house_low', value: house });
                                }
                            }
                        }
                    },
                    series: [{ name: 'Estimated contributors', data: values }],
                    labels: houses,
                    stroke: { width: 2 },
                    fill: { opacity: 0.25 },
                    colors: ['#f97316'],
                    title: { text: 'Contribution Spread (click to inspect non-contributors)' },
                    tooltip: { theme: 'dark' }
                });
                charts.contribution.render();
            }

            function renderRisk(data) {
                var houses = (data.house_breakdown && data.house_breakdown.categories) ? data.house_breakdown.categories : [];
                var source = chartDataSeries(data.house_breakdown ? data.house_breakdown.series : []);
                var values = source.map(function (v) { return Math.min(100, Math.floor(100 / (Number(v) + 1))); });

                charts.risk = new ApexCharts(document.querySelector('#house-risk'), {
                    chart: {
                        type: 'bar',
                        height: 360,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                var house = houses[config.dataPointIndex];
                                if (house) {
                                    drillDown({ type: 'house_low', value: house });
                                }
                            }
                        }
                    },
                    series: [{ name: 'Risk index', data: values }],
                    xaxis: { categories: houses },
                    colors: ['#ef4444'],
                    plotOptions: { bar: { borderRadius: 5 } },
                    title: { text: 'Underperformance Index (click for at-risk students)' },
                    tooltip: { theme: 'dark' }
                });
                charts.risk.render();
            }

            function renderMomentum(data) {
                var rawDates = (data.trend && data.trend.categories) ? data.trend.categories : [];
                var values = chartDataSeries(data.trend ? data.trend.series : []);
                var displayDates = rawDates.map(function (d) {
                    var date = new Date(d + 'T00:00:00');
                    return date.getDate() + ' ' + date.toLocaleString('en-AU', { month: 'short' });
                });

                var minValue = values.length ? Math.min.apply(null, values) : 0;
                var worstIndex = values.length ? values.indexOf(minValue) : 0;
                var points = [];
                if (displayDates[worstIndex] != null) {
                    points.push({
                        x: displayDates[worstIndex],
                        y: minValue,
                        label: { text: 'Lowest Day' }
                    });
                }

                charts.momentum = new ApexCharts(document.querySelector('#house-momentum'), {
                    chart: {
                        type: 'area',
                        height: 420,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                var rawDate = rawDates[config.dataPointIndex];
                                if (rawDate) {
                                    drillDown({ type: 'date', value: rawDate });
                                }
                            }
                        }
                    },
                    series: [{ name: 'Weekday points', data: values }],
                    xaxis: { type: 'category', categories: displayDates },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: { opacityFrom: 0.45, opacityTo: 0.05 }
                    },
                    annotations: { points: points },
                    colors: ['#a855f7'],
                    title: { text: 'House Momentum (click weakest day for detail)' },
                    tooltip: { theme: 'dark' }
                });
                charts.momentum.render();
            }

            function renderHouseCharts(data) {
                destroyCharts();
                renderRank(data);
                renderContribution(data);
                renderRisk(data);
                renderMomentum(data);
            }

            function fetchHouseData() {
                fetch(dataUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(renderHouseCharts)
                    .catch(function () {});
            }

            document.getElementById('hr-modal-close').addEventListener('click', function () {
                document.getElementById('hr-modal-backdrop').style.display = 'none';
            });
            document.getElementById('hr-modal-backdrop').addEventListener('click', function (e) {
                if (e.target.id === 'hr-modal-backdrop') {
                    document.getElementById('hr-modal-backdrop').style.display = 'none';
                }
            });

            document.addEventListener('DOMContentLoaded', fetchHouseData);
        })();
    </script>
@endpush
