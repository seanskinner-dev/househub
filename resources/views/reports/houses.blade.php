@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">House Performance Report</h1>
    <p style="font-size: 1rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 56rem;">
        House-focused insights emphasizing low or weakening outcomes. Click any chart element to drill into problem areas.
    </p>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card hh-card mb-4 h-100" style="grid-column: span 2;">
                <div class="card-body">
                    <h5 class="mb-2">House Points Over Time</h5>
                    <p class="text-muted small mb-3">
                        Shows how each house is tracking over time.
                        Use this to identify momentum and compare performance trends.
                    </p>
                    <div id="house-points-over-time" style="min-height: 340px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 h-100">
                <div class="card-body">
                    <h5 class="mb-2">Term Performance Comparison</h5>
                    <p class="text-muted small mb-3">
                        Compares house point totals between current and prior term windows. Use differences to identify houses that are stalling or improving.
                    </p>
                    <div id="house-comparison" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 h-100">
                <div class="card-body">
                    <h5 class="mb-2">House Momentum</h5>
                    <p class="text-muted small mb-3">
                        Tracks trend direction across selected dates for each house. Click low points to inspect weaker activity days.
                    </p>
                    <div id="house-momentum" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 h-100">
                <div class="card-body">
                    <h5 class="mb-2">Contribution Spread</h5>
                    <p class="text-muted small mb-3">
                        Highlights how broadly contributions are distributed within each house. Narrow spread can indicate over-reliance on a small group.
                    </p>
                    <div id="house-contribution" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 h-100">
                <div class="card-body">
                    <h5 class="mb-2">Underperformance Index</h5>
                    <p class="text-muted small mb-3">
                        Flags houses with higher relative risk or lower engagement output. Click bars to drill into students needing support.
                    </p>
                    <div id="house-risk" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="hr-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:1000;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#1e293b;color:#f1f5f9;max-width:920px;width:100%;max-height:86vh;overflow:auto;border-radius:10px;box-shadow:0 20px 50px rgba(0,0,0,0.5);">
            <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;">
                <h3 id="hr-modal-title" style="margin:0;font-size:1.1rem;">Details</h3>
                <button id="hr-modal-close" type="button" style="background:transparent;border:none;color:#fff;font-size:1.4rem;cursor:pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="hr-modal-body" style="padding:16px 18px;">
                <p id="hr-empty" style="margin:0;opacity:0.9;display:none;">No rows.</p>
                <div id="hr-wrap" style="display:none;overflow-x:auto;">
                    <table id="hr-drilldown-table" class="report-drilldown-table" style="font-size:0.95rem;">
                        <thead><tr id="hr-thead"></tr></thead>
                        <tbody id="hr-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var dataUrl = @json(route('reports.data'));
            var drillUrl = @json(route('reports.drilldown'));
            var charts = { overTime: null, rank: null, contribution: null, risk: null, momentum: null };
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
                if (typeof window.renderStudentTable !== 'function') {
                    console.warn('renderStudentTable is not available');
                    return;
                }
                window.renderStudentTable(data, {
                    title: document.getElementById('hr-modal-title'),
                    empty: document.getElementById('hr-empty'),
                    wrap: document.getElementById('hr-wrap'),
                    theadRow: document.getElementById('hr-thead'),
                    tbody: document.getElementById('hr-tbody'),
                    table: document.getElementById('hr-drilldown-table')
                });
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

                charts.rank = new ApexCharts(document.querySelector('#house-comparison'), window.hhApplyApexDefaults({
                    chart: {
                        type: 'bar',
                        height: 320,
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
                }));
                charts.rank.render();
            }

            function renderHousePointsOverTime(data) {
                var overTime = data.house_points_over_time || {};
                var rawDates = Array.isArray(overTime.categories) ? overTime.categories : [];
                var displayDates = rawDates.map(function (d) {
                    if (typeof d === 'string' && d.length >= 10) {
                        var dt = new Date(d + 'T00:00:00');
                        if (!isNaN(dt.getTime())) {
                            return dt.toLocaleDateString(undefined, { weekday: 'short' });
                        }
                    }
                    return typeof window.formatReportChartDate === 'function' ? window.formatReportChartDate(d) : String(d);
                });

                var series = Array.isArray(overTime.series) ? overTime.series : [];
                var colorsByHouse = {
                    Gryffindor: '#740001',
                    Slytherin: '#1a472a',
                    Ravenclaw: '#3b82f6',
                    Hufflepuff: '#ffcc00'
                };
                var colorList = series.map(function (s) {
                    return colorsByHouse[s.name] || '#0ea5e9';
                });

                charts.overTime = new ApexCharts(document.querySelector('#house-points-over-time'), window.hhApplyApexDefaults({
                    chart: {
                        type: 'line',
                        height: 340,
                        toolbar: { show: false }
                    },
                    series: series.map(function (s) {
                        return {
                            name: String(s.name || ''),
                            data: Array.isArray(s.data) ? s.data.map(function (v) { return Number(v) || 0; }) : []
                        };
                    }),
                    xaxis: {
                        categories: displayDates,
                        tickPlacement: 'on',
                        labels: {
                            rotate: -90,
                            style: {
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Points'
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 4
                    },
                    markers: {
                        size: 0
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.1)'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: colorList,
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (val, opts) {
                                var s = opts && opts.seriesIndex != null && series[opts.seriesIndex] ? series[opts.seriesIndex].name : 'House';
                                return s + ': ' + (Number(val) || 0);
                            }
                        }
                    }
                }));
                charts.overTime.render();
            }

            function renderContribution(data) {
                var houses = (data.house_breakdown && data.house_breakdown.categories) ? data.house_breakdown.categories : [];
                var source = chartDataSeries(data.house_breakdown ? data.house_breakdown.series : []);
                var values = source.map(function (v) { return Math.max(0, Math.floor(v / 10)); });
                var sortedContribution = houses.map(function (h, i) {
                    return { name: h, value: values[i] || 0 };
                }).sort(function (a, b) { return b.value - a.value; });
                houses = sortedContribution.map(function (r) { return r.name; });
                values = sortedContribution.map(function (r) { return r.value; });

                charts.contribution = new ApexCharts(document.querySelector('#house-contribution'), window.hhApplyApexDefaults({
                    chart: {
                        type: 'bar',
                        height: 320,
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
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            borderRadius: 6,
                            barHeight: '50%'
                        }
                    },
                    series: [{ name: 'Estimated contributors', data: values }],
                    xaxis: {
                        categories: houses,
                        labels: {
                            style: {
                                colors: '#94a3b8'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#e2e8f0',
                                fontSize: '13px'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        offsetX: 10,
                        style: {
                            colors: ['#f8fafc'],
                            fontSize: '12px',
                            fontWeight: 600
                        },
                        background: {
                            enabled: true,
                            fillColor: '#0f172a',
                            borderRadius: 6,
                            padding: 4,
                            opacity: 1
                        },
                        formatter: function (val) {
                            return String(val);
                        }
                    },
                    colors: ['#f59e0b'],
                    title: { text: 'Contribution Spread (click to inspect non-contributors)' },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (val) {
                                return String(val) + ' points';
                            }
                        }
                    }
                }));
                charts.contribution.render();
            }

            function renderRisk(data) {
                var houses = (data.underperformance_index && data.underperformance_index.categories) ? data.underperformance_index.categories : [];
                var source = chartDataSeries(data.underperformance_index ? data.underperformance_index.series : []);
                var values = source.map(function (v) { return Math.min(100, Math.floor(100 / (Number(v) + 1))); });
                var sortedRisk = houses.map(function (h, i) {
                    return { name: h, value: values[i] || 0 };
                }).sort(function (a, b) { return b.value - a.value; });
                houses = sortedRisk.map(function (r) { return r.name; });
                values = sortedRisk.map(function (r) { return r.value; });

                charts.risk = new ApexCharts(document.querySelector('#house-risk'), window.hhApplyApexDefaults({
                    chart: {
                        type: 'bar',
                        height: 320,
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
                }));
                charts.risk.render();
            }

            function renderMomentum(data) {
                var rawDates = (data.trend && data.trend.categories) ? data.trend.categories : [];
                var values = chartDataSeries(data.trend ? data.trend.series : []);
                var displayDates = rawDates.map(function (d) {
                    return typeof window.formatReportChartDate === 'function' ? window.formatReportChartDate(d) : String(d);
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

                charts.momentum = new ApexCharts(document.querySelector('#house-momentum'), window.hhApplyApexDefaults({
                    chart: {
                        type: 'area',
                        height: 320,
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
                    markers: { size: 4 },
                    dataLabels: { enabled: false },
                    fill: {
                        type: 'gradient',
                        gradient: { opacityFrom: 0.45, opacityTo: 0.05 }
                    },
                    annotations: { points: points },
                    colors: ['#a855f7'],
                    title: { text: 'House Momentum (click weakest day for detail)' },
                    tooltip: { theme: 'dark' }
                }));
                charts.momentum.render();
            }

            function renderHouseCharts(data) {
                try {
                    destroyCharts();
                    renderHousePointsOverTime(data);
                    renderRank(data);
                    renderContribution(data);
                    renderRisk(data);
                    renderMomentum(data);
                } catch (e) {
                    console.error('Chart render failed:', e);
                }
            }

            function fetchHouseData() {
                console.log('Fetching report data...');
                fetch(dataUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        console.log('Report data received:', data);
                        console.log('Chart data:', data);
                        renderHouseCharts(data);
                    })
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

