@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 52rem;">
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
            <label for="pc-year" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Year</label>
            <select id="pc-year" name="year" style="min-width: 140px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                <option value="7">Year 7</option>
                <option value="8">Year 8</option>
                <option value="9">Year 9</option>
                <option value="10">Year 10</option>
                <option value="11">Year 11</option>
                <option value="12">Year 12</option>
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

    <section style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by year level</h2>
        <div id="pc-year-chart" style="min-height: 420px;"></div>
    </section>

    <div id="pc-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div id="pc-modal" role="dialog" aria-modal="true" style="background: #1e293b; color: #f1f5f9; max-width: 900px; width: 100%; max-height: 85vh; overflow: auto; border-radius: 10px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #334155;">
                <h3 id="pc-modal-title" style="margin: 0; font-size: 1.2rem;">Details</h3>
                <button type="button" id="pc-modal-close" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; line-height: 1; cursor: pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="pc-modal-body" style="padding: 16px 20px;">
                <p id="pc-drilldown-empty" style="opacity:0.9;margin:0;display:none;">No rows for this selection.</p>
                <div id="pc-drilldown-wrap" style="display:none; overflow-x: auto;">
                    <table id="pc-drilldown-table" style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                        <thead><tr id="pc-drilldown-thead-row"></tr></thead>
                        <tbody id="pc-drilldown-tbody"></tbody>
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
                end_date: ymd(end),
                year: 'All'
            };

            var drilldownData = [];
            var drilldownSortDir = {};

            var charts = {
                donut: null,
                trend: null,
                house: null,
                year: null
            };

            var commonFont = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };

            function trendAxisLabelFormatter(value) {
                // Ensure we always parse correctly
                const d = new Date(value + 'T00:00:00');

                const day = d.getDate();
                const month = d.toLocaleString('en-AU', { month: 'short' });

                return `${day} ${month}`; // e.g. "10 Apr"
            }

            function trendRawDateFromConfig(config) {
                if (!config || config.dataPointIndex == null) {
                    return null;
                }
                var cats = config.w && config.w.config && config.w.config.xaxis && config.w.config.xaxis.categories;
                if (!cats || !cats.length) {
                    return null;
                }
                return cats[config.dataPointIndex];
            }

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            /**
             * Dark tooltips for all PC charts. Donut uses pie-style series[seriesIndex]; line/bar use series[si][dpi].
             */
            function pcCustomTooltip(valueSuffix) {
                return {
                    theme: false,
                    style: {
                        fontSize: '14px',
                        color: '#ffffff'
                    },
                    custom: function (ctx) {
                        var series = ctx.series;
                        var seriesIndex = ctx.seriesIndex;
                        var dataPointIndex = ctx.dataPointIndex;
                        var w = ctx.w;
                        var chartType = w && w.config && w.config.chart ? w.config.chart.type : '';

                        var value;
                        var label = '';

                        if (chartType === 'donut' || chartType === 'pie') {
                            value = series[seriesIndex];
                            if (w.globals.labels && w.globals.labels.length) {
                                label = w.globals.labels[seriesIndex];
                            }
                        } else {
                            var row = series[seriesIndex];
                            value = row != null ? row[dataPointIndex] : null;
                            if (w.globals.categoryLabels && w.globals.categoryLabels.length) {
                                label = w.globals.categoryLabels[dataPointIndex];
                            } else if (w.globals.labels && w.globals.labels.length) {
                                label = w.globals.labels[dataPointIndex];
                            }
                        }

                        if (label && /^\d{4}-\d{2}-\d{2}$/.test(String(label))) {
                            var d = new Date(String(label) + 'T12:00:00');
                            var day = d.getDate();
                            var month = d.toLocaleString('en-AU', { month: 'short' });
                            label = day + ' ' + month;
                        }

                        var v = value != null && value !== '' ? value : '—';

                        return (
                            '<div style="' +
                            'background:#1e293b;color:#ffffff;padding:10px 14px;border-radius:8px;' +
                            'font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,0.4);font-size:14px;' +
                            '">' +
                            (label ? escapeHtml(String(label)) : '') +
                            (label ? '<br/>' : '') +
                            valueSuffix + ': ' + escapeHtml(String(v)) +
                            '</div>'
                        );
                    }
                };
            }

            function syncFiltersFromDom() {
                filters.house = document.getElementById('pc-house').value || 'All';
                filters.start_date = document.getElementById('pc-start').value || null;
                filters.end_date = document.getElementById('pc-end').value || null;
                filters.year = document.getElementById('pc-year').value || 'All';
            }

            function syncDomFromFilters() {
                document.getElementById('pc-house').value = filters.house;
                document.getElementById('pc-start').value = filters.start_date || '';
                document.getElementById('pc-end').value = filters.end_date || '';
                document.getElementById('pc-year').value = filters.year || 'All';
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
                p.set('year', filters.year || 'All');
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
                p.set('year', filters.year || 'All');
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

            function drilldownIsDateKey(key) {
                return key === 'created_at' || key === 'when' || /_at$/i.test(String(key));
            }

            function drilldownCompare(a, b, key, ascending) {
                var valA = a[key];
                var valB = b[key];
                var mul = ascending ? 1 : -1;
                if (drilldownIsDateKey(key)) {
                    var ta = new Date(valA).getTime();
                    var tb = new Date(valB).getTime();
                    if (isNaN(ta) || isNaN(tb)) {
                        return mul * String(valA == null ? '' : valA).localeCompare(String(valB == null ? '' : valB));
                    }
                    return mul * (ta - tb);
                }
                var na = Number(valA);
                var nb = Number(valB);
                if (!isNaN(na) && !isNaN(nb) && String(valA).trim() !== '' && String(valB).trim() !== '') {
                    return mul * (na - nb);
                }
                return mul * String(valA == null ? '' : valA).localeCompare(String(valB == null ? '' : valB), undefined, { sensitivity: 'base' });
            }

            function renderDrilldownTableBody(rows) {
                var keys = rows.length ? Object.keys(rows[0]) : [];
                var tbody = document.getElementById('pc-drilldown-tbody');
                var html = '';
                rows.forEach(function (row) {
                    html += '<tr style="border-bottom:1px solid #334155;">';
                    keys.forEach(function (k) {
                        var v = row[k];
                        html += '<td style="padding:10px 12px;">' + escapeHtml(v == null ? '' : String(v)) + '</td>';
                    });
                    html += '</tr>';
                });
                tbody.innerHTML = html;
            }

            function renderDrillDownModal(data) {
                var title = data.title || 'Details';
                var rows = data.rows || [];
                document.getElementById('pc-modal-title').textContent = title;
                var emptyEl = document.getElementById('pc-drilldown-empty');
                var wrapEl = document.getElementById('pc-drilldown-wrap');
                var theadRow = document.getElementById('pc-drilldown-thead-row');
                if (!rows.length) {
                    drilldownData = [];
                    emptyEl.style.display = 'block';
                    wrapEl.style.display = 'none';
                    theadRow.innerHTML = '';
                    document.getElementById('pc-drilldown-tbody').innerHTML = '';
                } else {
                    emptyEl.style.display = 'none';
                    wrapEl.style.display = 'block';
                    drilldownData = rows.map(function (r) {
                        return Object.assign({}, r);
                    });
                    var keys = Object.keys(rows[0]);
                    var headHtml = '';
                    keys.forEach(function (k) {
                        headHtml +=
                            '<th data-sort="' +
                            escapeHtml(k) +
                            '" style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;cursor:pointer;user-select:none;" title="Sort">' +
                            escapeHtml(k) +
                            '</th>';
                    });
                    theadRow.innerHTML = headHtml;
                    renderDrilldownTableBody(drilldownData);
                }
                document.getElementById('pc-modal-backdrop').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('pc-modal-backdrop').style.display = 'none';
            }

            document.getElementById('pc-modal-body').addEventListener('click', function (e) {
                var th = e.target.closest('th[data-sort]');
                if (!th || !document.getElementById('pc-drilldown-table').contains(th)) {
                    return;
                }
                var key = th.getAttribute('data-sort');
                if (!key || !drilldownData.length) {
                    return;
                }
                drilldownSortDir[key] = !drilldownSortDir[key];
                var ascending = !!drilldownSortDir[key];
                var sorted = drilldownData.slice().sort(function (a, b) {
                    return drilldownCompare(a, b, key, ascending);
                });
                renderDrilldownTableBody(sorted);
            });

            function updateAllCharts(data) {
                if (charts.donut && data.donut) {
                    charts.donut.updateOptions({ labels: data.donut.labels });
                    charts.donut.updateSeries(data.donut.series);
                }
                if (charts.trend && data.trend) {
                    charts.trend.updateOptions({
                        xaxis: {
                            type: 'category',
                            categories: data.trend.categories,
                            labels: {
                                rotate: -45,
                                formatter: trendAxisLabelFormatter
                            }
                        }
                    });
                    charts.trend.updateSeries([{ name: 'Points', data: data.trend.series }]);
                }
                if (charts.house && data.house_breakdown) {
                    charts.house.updateOptions({ xaxis: { categories: data.house_breakdown.categories } });
                    charts.house.updateSeries([{ name: 'Points', data: data.house_breakdown.series }]);
                }
                if (charts.year && data.year_level) {
                    var ycats = (data.year_level.categories || []).map((y) => `Year ${y}`);
                    charts.year.updateOptions({ xaxis: { categories: ycats } });
                    charts.year.updateSeries([{ name: 'Points', data: data.year_level.series }]);
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
                                if (event) {
                                    if (event.preventDefault) {
                                        event.preventDefault();
                                    }
                                    if (event.stopPropagation) {
                                        event.stopPropagation();
                                    }
                                }
                                const label = config.w.config.labels[config.dataPointIndex];
                                let mappedLabel = '';
                                const lower = String(label || '').toLowerCase();
                                if (lower.includes('low')) {
                                    mappedLabel = 'Low';
                                } else if (lower.includes('medium')) {
                                    mappedLabel = 'Medium';
                                } else if (lower.includes('high')) {
                                    mappedLabel = 'High';
                                } else {
                                    mappedLabel = label;
                                }
                                if (mappedLabel) {
                                    drillDown(mappedLabel);
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
                    tooltip: pcCustomTooltip('Students')
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
                                if (event) {
                                    if (event.preventDefault) {
                                        event.preventDefault();
                                    }
                                    if (event.stopPropagation) {
                                        event.stopPropagation();
                                    }
                                }
                                var rawDate = trendRawDateFromConfig(config);
                                if (rawDate) {
                                    drillDown(String(rawDate));
                                }
                            },
                            markerClick: function (event, chartContext, config) {
                                if (event) {
                                    if (event.preventDefault) {
                                        event.preventDefault();
                                    }
                                    if (event.stopPropagation) {
                                        event.stopPropagation();
                                    }
                                }
                                var rawDate = trendRawDateFromConfig(config);
                                if (rawDate) {
                                    drillDown(String(rawDate));
                                }
                            }
                        }
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 6, hover: { size: 9 } },
                    xaxis: {
                        type: 'category',
                        categories: [],
                        labels: {
                            rotate: -45,
                            formatter: trendAxisLabelFormatter
                        }
                    },
                    yaxis: { labels: { style: { fontSize: '13px' } } },
                    grid: { borderColor: '#334155' },
                    tooltip: {
                        enabled: true,
                        theme: 'dark'
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
                                    if (event) {
                                        if (event.preventDefault) {
                                            event.preventDefault();
                                        }
                                        if (event.stopPropagation) {
                                            event.stopPropagation();
                                        }
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
                        tooltip: pcCustomTooltip('Points')
                    });
                };

                charts.house = barChartFactory('#pc-house-chart', 'Points');
                charts.year = barChartFactory('#pc-year-chart', 'Points');
                charts.house.render();
                charts.year.render();
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
