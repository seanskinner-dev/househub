@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.5rem; max-width: 52rem;">
        Pastoral care overview — weekday data only (Mon–Fri). Use filters and click charts for details (no page reload).
    </p>

    <div id="pc-filter-bar" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; margin-bottom: 2.5rem; padding: 18px; background: #1e293b; border-radius: 8px;">
        <div>
            <label for="pc-house" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">House</label>
            <select id="pc-house" name="house" style="min-width: 180px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                @foreach ($houses as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="pc-start" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Start date</label>
            <input type="date" id="pc-start" name="start_date" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="pc-end" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">End date</label>
            <input type="date" id="pc-end" name="end_date" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <button type="button" id="pc-apply" style="padding: 11px 22px; font-size: 1rem; font-weight: 600; border: none; border-radius: 6px; background: #3b82f6; color: #fff; cursor: pointer;">
                Apply
            </button>
        </div>
    </div>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Risk distribution</h2>
        <div style="max-width: 460px;">
            <div id="pc-risk-chart"></div>
        </div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Engagement trend (weekdays in range)</h2>
        <div id="pc-trend-chart" style="min-height: 400px;"></div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by house</h2>
        <div id="pc-house-chart" style="min-height: 420px;"></div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by year level</h2>
        <div id="pc-year-chart" style="min-height: 420px;"></div>
    </section>

    <section style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by category</h2>
        <div id="pc-category-chart" style="min-height: 420px;"></div>
    </section>

    <div id="pc-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div id="pc-modal" role="dialog" aria-modal="true" style="background: #1e293b; color: #f1f5f9; max-width: 900px; width: 100%; max-height: 85vh; overflow: auto; border-radius: 10px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #334155;">
                <h3 id="pc-modal-title" style="margin: 0; font-size: 1.2rem;">Details</h3>
                <button type="button" id="pc-modal-close" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; line-height: 1; cursor: pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="pc-modal-body" style="padding: 16px 20px;"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
    <script>
        (function () {
            var dataUrl = @json(route('reports.data'));
            var drillUrl = @json(route('reports.drilldown'));

            function ymd(d) {
                var y = d.getFullYear();
                var m = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + day;
            }

            var end = new Date();
            var start = new Date();
            start.setDate(start.getDate() - 29);

            let filters = {
                house: 'All',
                start_date: ymd(start),
                end_date: ymd(end)
            };

            var charts = {
                donut: null,
                trend: null,
                house: null,
                year: null,
                category: null
            };

            var commonFont = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };

            function syncFiltersFromDom() {
                filters.house = document.getElementById('pc-house').value || 'All';
                filters.start_date = document.getElementById('pc-start').value || null;
                filters.end_date = document.getElementById('pc-end').value || null;
            }

            function syncDomFromFilters() {
                document.getElementById('pc-house').value = filters.house;
                document.getElementById('pc-start').value = filters.start_date || '';
                document.getElementById('pc-end').value = filters.end_date || '';
            }

            function chartsQueryString() {
                var p = new URLSearchParams();
                p.set('house', filters.house);
                if (filters.start_date) {
                    p.set('start_date', filters.start_date);
                }
                if (filters.end_date) {
                    p.set('end_date', filters.end_date);
                }
                return p.toString();
            }

            function drillQueryString(label) {
                var p = new URLSearchParams();
                p.set('label', label);
                p.set('house', filters.house);
                if (filters.start_date) {
                    p.set('start_date', filters.start_date);
                }
                if (filters.end_date) {
                    p.set('end_date', filters.end_date);
                }
                return p.toString();
            }

            function fetchCharts() {
                fetch(dataUrl + '?' + chartsQueryString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        updateAllCharts(data);
                    })
                    .catch(function () {});
            }

            function drillDown(label) {
                fetch(drillUrl + '?' + drillQueryString(label), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        renderDrillDownModal(data);
                    })
                    .catch(function () {});
            }

            function renderDrillDownModal(data) {
                var title = data.title || 'Details';
                var rows = data.rows || [];
                document.getElementById('pc-modal-title').textContent = title;
                var body = document.getElementById('pc-modal-body');
                if (!rows.length) {
                    body.innerHTML = '<p style="opacity:0.9;margin:0;">No rows for this selection.</p>';
                } else {
                    var keys = Object.keys(rows[0]);
                    var html = '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:0.95rem;"><thead><tr>';
                    keys.forEach(function (k) {
                        html += '<th style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;">' + escapeHtml(k) + '</th>';
                    });
                    html += '</tr></thead><tbody>';
                    rows.forEach(function (row) {
                        html += '<tr style="border-bottom:1px solid #334155;">';
                        keys.forEach(function (k) {
                            var v = row[k];
                            html += '<td style="padding:10px 12px;">' + escapeHtml(v == null ? '' : String(v)) + '</td>';
                        });
                        html += '</tr>';
                    });
                    html += '</tbody></table></div>';
                    body.innerHTML = html;
                }
                document.getElementById('pc-modal-backdrop').style.display = 'flex';
            }

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function closeModal() {
                document.getElementById('pc-modal-backdrop').style.display = 'none';
            }

            function updateAllCharts(data) {
                if (charts.donut && data.donut) {
                    charts.donut.updateOptions({ labels: data.donut.labels });
                    charts.donut.updateSeries(data.donut.series);
                }
                if (charts.trend && data.trend) {
                    charts.trend.updateOptions({ xaxis: { categories: data.trend.categories } });
                    charts.trend.updateSeries([{ name: 'Points', data: data.trend.series }]);
                }
                if (charts.house && data.house_breakdown) {
                    charts.house.updateOptions({ xaxis: { categories: data.house_breakdown.categories } });
                    charts.house.updateSeries([{ name: 'Points', data: data.house_breakdown.series }]);
                }
                if (charts.year && data.year_level) {
                    charts.year.updateOptions({ xaxis: { categories: data.year_level.categories } });
                    charts.year.updateSeries([{ name: 'Points', data: data.year_level.series }]);
                }
                if (charts.category && data.category) {
                    charts.category.updateOptions({ xaxis: { categories: data.category.categories } });
                    charts.category.updateSeries([{ name: 'Points', data: data.category.series }]);
                }
            }

            function createCharts() {
                charts.donut = new ApexCharts(document.querySelector('#pc-risk-chart'), {
                    series: [0, 0, 0],
                    labels: ['High Risk', 'Medium Risk', 'Active'],
                    chart: {
                        type: 'donut',
                        height: 400,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        selection: { enabled: true },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                if (event && event.preventDefault) {
                                    event.preventDefault();
                                }
                                var index = config.dataPointIndex;
                                var label;
                                if (config.w && config.w.config && config.w.config.labels) {
                                    label = config.w.config.labels[index];
                                }
                                if (label) {
                                    drillDown(label);
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
                    tooltip: { y: { formatter: function (val) { return val + ' students'; } } }
                });
                charts.donut.render();

                charts.trend = new ApexCharts(document.querySelector('#pc-trend-chart'), {
                    series: [{ name: 'Points', data: [] }],
                    chart: {
                        type: 'line',
                        height: 420,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        selection: { enabled: true },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                if (event && event.preventDefault) {
                                    event.preventDefault();
                                }
                                var index = config.dataPointIndex;
                                var label;
                                if (config.w && config.w.config && config.w.config.xaxis && config.w.config.xaxis.categories) {
                                    label = config.w.config.xaxis.categories[index];
                                }
                                if (label) {
                                    drillDown(label);
                                }
                            }
                        }
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, hover: { size: 8 } },
                    xaxis: { categories: [] },
                    yaxis: { labels: { style: { fontSize: '13px' } } },
                    grid: { borderColor: '#334155' },
                    tooltip: {
                        y: { formatter: function (val) { return val + ' pts'; } }
                    }
                });
                charts.trend.render();

                var barChartFactory = function (el, name) {
                    return new ApexCharts(document.querySelector(el), {
                        series: [{ name: name, data: [] }],
                        chart: {
                            type: 'bar',
                            height: 440,
                            fontFamily: commonFont.fontFamily,
                            foreColor: commonFont.foreColor,
                            toolbar: { show: false },
                            selection: { enabled: true },
                            events: {
                                dataPointSelection: function (event, chartContext, config) {
                                    if (event && event.preventDefault) {
                                        event.preventDefault();
                                    }
                                    var index = config.dataPointIndex;
                                    var label;
                                    if (config.w && config.w.config && config.w.config.xaxis && config.w.config.xaxis.categories) {
                                        label = config.w.config.xaxis.categories[index];
                                    }
                                    if (label) {
                                        drillDown(label);
                                    }
                                }
                            }
                        },
                        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                        colors: ['#3b82f6'],
                        xaxis: { categories: [], labels: { style: { fontSize: '13px' } } },
                        yaxis: { labels: { style: { fontSize: '13px' } }, min: 0 },
                        grid: { borderColor: '#334155' },
                        dataLabels: { enabled: true, style: { fontSize: '11px' } },
                        tooltip: { y: { formatter: function (val) { return val + ' pts'; } } }
                    });
                };

                charts.house = barChartFactory('#pc-house-chart', 'Points');
                charts.year = barChartFactory('#pc-year-chart', 'Points');
                charts.category = barChartFactory('#pc-category-chart', 'Points');
                charts.house.render();
                charts.year.render();
                charts.category.render();
            }

            document.getElementById('pc-apply').addEventListener('click', function () {
                syncFiltersFromDom();
                fetchCharts();
            });

            document.getElementById('pc-modal-close').addEventListener('click', closeModal);
            document.getElementById('pc-modal-backdrop').addEventListener('click', function (e) {
                if (e.target.id === 'pc-modal-backdrop') {
                    closeModal();
                }
            });

            syncDomFromFilters();
            createCharts();
            fetchCharts();
        })();
    </script>
@endpush
