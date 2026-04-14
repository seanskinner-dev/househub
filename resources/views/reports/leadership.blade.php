@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">Leadership Report</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 52rem;">
        Snapshot views over the same filters as pastoral care — chart types here are different from the PC dashboard. Click any chart for drill-down (no page reload).
    </p>

    <div id="lr-filter-bar" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; margin-bottom: 2rem; padding: 18px; background: #1e293b; border-radius: 8px;">
        <div>
            <label for="lr-house" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">House</label>
            <select id="lr-house" style="min-width: 180px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All Houses</option>
                @foreach ($houses as $house)
                    <option value="{{ $house->name }}">{{ $house->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="lr-start" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Start date</label>
            <input type="date" id="lr-start" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="lr-end" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">End date</label>
            <input type="date" id="lr-end" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="lr-year" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Year</label>
            <select id="lr-year" style="min-width: 140px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All Years</option>
                <option value="7">Year 7</option>
                <option value="8">Year 8</option>
                <option value="9">Year 9</option>
                <option value="10">Year 10</option>
                <option value="11">Year 11</option>
                <option value="12">Year 12</option>
            </select>
        </div>
        <div>
            <button type="button" id="lr-apply" style="padding: 11px 22px; font-size: 1rem; font-weight: 600; border: none; border-radius: 6px; background: #0ea5e9; color: #fff; cursor: pointer;">
                Apply
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Engagement Health</h5>
                    <p class="text-muted small mb-3">
                        Shows the proportion of students receiving points in the selected range. Higher values indicate stronger day-to-day engagement.
                    </p>
                    <div id="engagement-health" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Weekday Activity Heatmap</h5>
                    <p class="text-muted small mb-3">
                        Visualises activity concentration across weekdays. Darker cells indicate stronger engagement intensity.
                    </p>
                    <div id="heatmap" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Points by Year Level</h5>
                    <p class="text-muted small mb-3">
                        Compares engagement totals across year cohorts. Click a point to inspect year-level student detail.
                    </p>
                    <div id="year-level" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Risk Mix</h5>
                    <p class="text-muted small mb-3">
                        Breaks the cohort into risk bands for quick comparison. Use segment clicks to drill into targeted intervention groups.
                    </p>
                    <div id="risk-mix" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Students Without Weekday Points</h5>
                    <p class="text-muted small mb-3">
                        Highlights students with no weekday point activity in range. Use this as a direct indicator for engagement drop-off.
                    </p>
                    <div id="lr-dropoff" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="lr-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div id="lr-modal" role="dialog" aria-modal="true" style="background: #1e293b; color: #f1f5f9; max-width: 900px; width: 100%; max-height: 85vh; overflow: auto; border-radius: 10px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px;">
                <h3 id="lr-modal-title" style="margin: 0; font-size: 1.2rem;">Details</h3>
                <button type="button" id="lr-modal-close" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; line-height: 1; cursor: pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="lr-modal-body" style="padding: 16px 20px;">
                <p id="lr-drilldown-empty" style="opacity:0.9;margin:0;display:none;">No rows for this selection.</p>
                <div id="lr-drilldown-wrap" style="display:none; overflow-x: auto;">
                    <table id="lr-drilldown-table" class="report-drilldown-table" style="font-size:0.95rem;">
                        <thead><tr id="lr-drilldown-thead-row"></tr></thead>
                        <tbody id="lr-drilldown-tbody"></tbody>
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

            var lrCharts = {
                health: null,
                heatmap: null,
                yearTrend: null,
                distribution: null,
                dropoff: null
            };

            var commonFont = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };

            function ymd(d) {
                var y = d.getFullYear();
                var m = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + day;
            }

            var end = new Date();
            var start = new Date();
            start.setDate(start.getDate() - 29);

            var filters = {
                house: 'All',
                start_date: ymd(start),
                end_date: ymd(end),
                year: 'All'
            };

            var lrDrilldownData = [];
            var lrCurrentSort = { key: null, direction: 'asc' };

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function syncDomFromFilters() {
                document.getElementById('lr-house').value = filters.house;
                document.getElementById('lr-start').value = filters.start_date || '';
                document.getElementById('lr-end').value = filters.end_date || '';
                document.getElementById('lr-year').value = filters.year || 'All';
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

            function drillDown(payload) {
                if (!payload || typeof payload !== 'object') {
                    return;
                }
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                var qs = chartsQueryString();
                var url = drillUrl + (qs ? '?' + qs : '');
                fetch(url, {
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
                    .then(function (data) {
                        renderDrillDownModal(data);
                    })
                    .catch(function () {});
            }

            function lrDrilldownIsDateKey(key) {
                return key === 'created_at' || key === 'when' || /_at$/i.test(String(key));
            }

            function lrDrilldownCompare(a, b, key, ascending) {
                var valA = a[key];
                var valB = b[key];
                var mul = ascending ? 1 : -1;
                if (lrDrilldownIsDateKey(key)) {
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

            function lrFriendlyKey(k) {
                var map = { first_name: 'First Name', last_name: 'Last Name', year_level: 'Year Level', activity_count: 'Activity', name: 'Name', house_name: 'House' };
                return map[k] || String(k).replace(/_/g, ' ').replace(/\b\w/g, function (s) { return s.toUpperCase(); });
            }

            function lrNormalizeRows(rows) {
                return rows.map(function (r) {
                    var o = Object.assign({}, r);
                    if (o.id != null) {
                        o._studentId = o.id;
                        delete o.id;
                    }
                    if (Object.prototype.hasOwnProperty.call(o, 'first_name') && Object.prototype.hasOwnProperty.call(o, 'last_name')) {
                        o.name = ((o.first_name || '') + ' ' + (o.last_name || '')).trim() || '—';
                        delete o.first_name;
                        delete o.last_name;
                    }
                    return o;
                });
            }

            function renderLrDrilldownTableBody(rows) {
                var keys = rows.length ? Object.keys(rows[0]).filter(function (k) { return k.indexOf('_') !== 0; }) : [];
                var tbody = document.getElementById('lr-drilldown-tbody');
                var html = '';
                rows.forEach(function (row) {
                    html += '<tr class="report-drilldown-row">';
                    keys.forEach(function (k) {
                        var v = row[k];
                        if (k === 'name' && row._studentId != null) {
                            html += '<td class="td-name" style="text-align:left;"><a href="/students/' + encodeURIComponent(String(row._studentId)) + '" class="student-link">' + escapeHtml(v == null ? '' : String(v)) + '</a></td>';
                        } else {
                            html += '<td style="padding:12px 14px;vertical-align:middle;">' + escapeHtml(v == null ? '' : String(v)) + '</td>';
                        }
                    });
                    html += '</tr>';
                });
                tbody.innerHTML = html;
            }

            function renderDrillDownModal(data) {
                var title = data.title || 'Details';
                var rows = data.rows || [];
                document.getElementById('lr-modal-title').textContent = title;
                var emptyEl = document.getElementById('lr-drilldown-empty');
                var wrapEl = document.getElementById('lr-drilldown-wrap');
                var theadRow = document.getElementById('lr-drilldown-thead-row');
                if (!rows.length) {
                    lrDrilldownData = [];
                    emptyEl.style.display = 'block';
                    wrapEl.style.display = 'none';
                    theadRow.innerHTML = '';
                    document.getElementById('lr-drilldown-tbody').innerHTML = '';
                } else {
                    emptyEl.style.display = 'none';
                    wrapEl.style.display = 'block';
                    lrCurrentSort = { key: null, direction: 'asc' };
                    lrDrilldownData = lrNormalizeRows(rows.map(function (r) { return Object.assign({}, r); }));
                    var keys = Object.keys(lrDrilldownData[0]).filter(function (k) { return k.indexOf('_') !== 0; });
                    var headHtml = '';
                    keys.forEach(function (k) {
                        headHtml +=
                            '<th data-sort-key="' +
                            escapeHtml(k) +
                            '" style="text-align:left;padding:10px 14px;border-bottom:2px solid #334155;cursor:pointer;user-select:none;" title="Sort">' +
                            escapeHtml(lrFriendlyKey(k)) +
                            '</th>';
                    });
                    theadRow.innerHTML = headHtml;
                    renderLrDrilldownTableBody(lrDrilldownData);
                }
                document.getElementById('lr-modal-backdrop').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('lr-modal-backdrop').style.display = 'none';
            }

            document.getElementById('lr-modal-body').addEventListener('click', function (e) {
                var th = e.target.closest('th[data-sort-key]');
                if (!th || !document.getElementById('lr-drilldown-table').contains(th)) {
                    return;
                }
                var key = th.getAttribute('data-sort-key');
                if (!key || !lrDrilldownData.length) {
                    return;
                }
                if (lrCurrentSort.key === key) {
                    lrCurrentSort.direction = lrCurrentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    lrCurrentSort.key = key;
                    lrCurrentSort.direction = 'asc';
                }
                var ascending = lrCurrentSort.direction === 'asc';
                var sorted = lrDrilldownData.slice().sort(function (a, b) {
                    return lrDrilldownCompare(a, b, key, ascending);
                });
                lrDrilldownData = sorted;
                renderLrDrilldownTableBody(sorted);
            });

            function destroyLrCharts() {
                Object.keys(lrCharts).forEach(function (k) {
                    if (lrCharts[k]) {
                        lrCharts[k].destroy();
                        lrCharts[k] = null;
                    }
                });
            }

            function stopEvent(event) {
                if (event) {
                    if (event.preventDefault) {
                        event.preventDefault();
                    }
                    if (event.stopPropagation) {
                        event.stopPropagation();
                    }
                }
            }

            function donutCounts(data) {
                var s = (data.donut && data.donut.series) ? data.donut.series : [0, 0, 0];
                var high = Number(s[0]) || 0;
                var medium = Number(s[1]) || 0;
                var active = Number(s[2]) || 0;
                var total = high + medium + active;
                return { high: high, medium: medium, active: active, total: total };
            }

            function renderHealth(data) {
                var d = donutCounts(data);
                var total = d.total > 0 ? d.total : 1;
                var percent = Math.round((d.active / total) * 100);

                var options = {
                    series: [percent],
                    chart: {
                        type: 'radialBar',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        events: {
                            click: function (event) {
                                stopEvent(event);
                                drillDown({ type: 'engagement_active' });
                            }
                        }
                    },
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '62%' },
                            dataLabels: {
                                name: { show: true, fontSize: '15px', color: '#94a3b8' },
                                value: {
                                    fontSize: '28px',
                                    fontWeight: 700,
                                    formatter: function (val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    labels: ['Engagement'],
                    colors: ['#22c55e']
                };

                lrCharts.health = new ApexCharts(document.querySelector('#engagement-health'), options);
                lrCharts.health.render();
            }

            function renderHeatmap(data) {
                var cats = (data.trend && data.trend.categories) ? data.trend.categories : [];
                var ser = (data.trend && data.trend.series) ? data.trend.series : [];
                window.lrHeatmapRawDates = cats.slice();
                var points = cats.map(function (c, i) {
                    var disp = typeof window.formatReportChartDate === 'function' ? window.formatReportChartDate(c) : String(c);
                    return { x: disp, y: Number(ser[i]) || 0 };
                });

                var options = {
                    series: [{ name: 'Weekday points', data: points }],
                    chart: {
                        type: 'heatmap',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                stopEvent(event);
                                var di = config.dataPointIndex;
                                var raw = window.lrHeatmapRawDates && window.lrHeatmapRawDates[di] != null
                                    ? window.lrHeatmapRawDates[di]
                                    : null;
                                if (raw) {
                                    drillDown({ type: 'date', value: String(raw) });
                                }
                            }
                        }
                    },
                    dataLabels: { enabled: true },
                    colors: ['#0ea5e9'],
                    xaxis: { type: 'category', labels: { rotate: -45 } },
                    tooltip: { theme: 'dark' },
                    grid: { borderColor: '#334155' }
                };

                lrCharts.heatmap = new ApexCharts(document.querySelector('#heatmap'), options);
                lrCharts.heatmap.render();
            }

            function renderYearTrend(data) {
                var cats = (data.year_level && data.year_level.categories) ? data.year_level.categories : [];
                var ser = (data.year_level && data.year_level.series) ? data.year_level.series : [];
                var xcats = cats.map(function (y) {
                    return 'Year ' + y;
                });

                var options = {
                    series: [{ name: 'Points', data: ser.map(function (v) { return Number(v) || 0; }) }],
                    chart: {
                        type: 'area',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                stopEvent(event);
                                var catsInner = config.w.config.xaxis.categories;
                                var label = catsInner[config.dataPointIndex];
                                if (label) {
                                    drillDown({ type: 'year_level', value: String(label) });
                                }
                            },
                            markerClick: function (event, chartContext, config) {
                                stopEvent(event);
                                var catsInner = config.w.config.xaxis.categories;
                                var label = catsInner[config.dataPointIndex];
                                if (label) {
                                    drillDown({ type: 'year_level', value: String(label) });
                                }
                            }
                        }
                    },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05 }
                    },
                    xaxis: { categories: xcats, labels: { style: { fontSize: '13px' } } },
                    yaxis: { labels: { style: { fontSize: '13px' } }, min: 0 },
                    grid: { borderColor: '#334155' },
                    colors: ['#a855f7'],
                    tooltip: { theme: 'dark' }
                };

                lrCharts.yearTrend = new ApexCharts(document.querySelector('#year-level'), options);
                lrCharts.yearTrend.render();
            }

            function renderDistribution(data) {
                var labels = (data.donut && data.donut.labels) ? data.donut.labels : ['High Risk', 'Medium Risk', 'Active'];
                var series = (data.donut && data.donut.series) ? data.donut.series.map(function (v) { return Number(v) || 0; }) : [0, 0, 0];

                var options = {
                    series: series,
                    labels: labels,
                    chart: {
                        type: 'polarArea',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event, chartContext, config) {
                                stopEvent(event);
                                var idx = config.dataPointIndex;
                                var lbl = labels[idx];
                                if (lbl) {
                                    drillDown({ type: 'risk_segment', value: String(lbl) });
                                }
                            }
                        }
                    },
                    stroke: { colors: ['#0f172a'] },
                    fill: { opacity: 0.85 },
                    colors: ['#ef4444', '#f59e0b', '#22c55e'],
                    legend: { position: 'bottom', fontSize: '14px' },
                    tooltip: { theme: 'dark' },
                    yaxis: { show: false }
                };

                lrCharts.distribution = new ApexCharts(document.querySelector('#risk-mix'), options);
                lrCharts.distribution.render();
            }

            function renderDropoff(data) {
                var d = donutCounts(data);
                var count = d.high + d.medium;

                var options = {
                    series: [{ name: 'Students', data: [count] }],
                    chart: {
                        type: 'bar',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        events: {
                            dataPointSelection: function (event) {
                                stopEvent(event);
                                drillDown({ type: 'engagement_low' });
                            }
                        }
                    },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '38%' } },
                    colors: ['#f59e0b'],
                    xaxis: {
                        categories: ['No weekday points (in range)'],
                        labels: { style: { fontSize: '13px' } }
                    },
                    yaxis: { labels: { style: { fontSize: '13px' } }, min: 0, tickAmount: 4 },
                    grid: { borderColor: '#334155' },
                    dataLabels: { enabled: true },
                    tooltip: { theme: 'dark' }
                };

                lrCharts.dropoff = new ApexCharts(document.querySelector('#lr-dropoff'), options);
                lrCharts.dropoff.render();
            }

            function renderLeadership(data) {
                try {
                    destroyLrCharts();
                    renderHealth(data);
                    renderHeatmap(data);
                    renderYearTrend(data);
                    renderDistribution(data);
                    renderDropoff(data);
                } catch (e) {
                    console.error('Chart render failed:', e);
                }
            }

            function fetchLeadership() {
                fetch(dataUrl + '?' + chartsQueryString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        console.log('Chart data:', data);
                        renderLeadership(data);
                    })
                    .catch(function () {});
            }

            document.getElementById('lr-apply').addEventListener('click', function () {
                filters.house = document.getElementById('lr-house').value || 'All';
                filters.start_date = document.getElementById('lr-start').value || null;
                filters.end_date = document.getElementById('lr-end').value || null;
                filters.year = document.getElementById('lr-year').value || 'All';
                fetchLeadership();
            });

            document.getElementById('lr-modal-close').addEventListener('click', closeModal);
            document.getElementById('lr-modal-backdrop').addEventListener('click', function (e) {
                if (e.target.id === 'lr-modal-backdrop') {
                    closeModal();
                }
            });

            syncDomFromFilters();
            fetchLeadership();
        })();
    </script>
@endpush

