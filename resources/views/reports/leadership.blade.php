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
            <input type="text" class="date-picker" id="lr-start" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="lr-end" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">End date</label>
            <input type="text" class="date-picker" id="lr-end" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
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
            <div class="card hh-card mb-4 h-100">
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
            <div class="card hh-card mb-4 h-100">
                <div class="card-body">
                    <h5 class="mb-2">Weekday Activity Heatmap</h5>
                    <p class="text-muted small mb-3">
                        Visualises activity concentration across weekdays. Darker cells indicate stronger engagement intensity.
                    </p>
                    <div id="heatmap" style="min-height: 320px;"></div>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:10px;">
                        <span style="font-size:12px;color:#cbd5e1;">Low</span>
                        <div style="flex:1;height:10px;border-radius:999px;background:linear-gradient(90deg,#0f172a 0%,#1d4ed8 35%,#0ea5e9 65%,#22c55e 100%);border:1px solid #334155;"></div>
                        <span style="font-size:12px;color:#cbd5e1;">High</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 h-100">
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
            <div class="card hh-card mb-4 h-100">
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
            <div class="card hh-card mb-4 h-100">
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
                    <div id="lr-drilldown-single-wrap">
                        <table id="lr-drilldown-table" class="report-drilldown-table" style="font-size:0.95rem;">
                            <thead><tr id="lr-drilldown-thead-row"></tr></thead>
                            <tbody id="lr-drilldown-tbody"></tbody>
                        </table>
                    </div>
                    <div id="lr-drilldown-grouped-host" style="display:none;"></div>
                </div>
                <div id="lr-risk-tabs-wrap" style="display:none;">
                    <ul class="nav nav-tabs mb-3" id="riskTabs">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#high-risk">High Risk</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#medium-risk">Medium Risk</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#low-risk">Low Risk</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="high-risk"></div>
                        <div class="tab-pane fade" id="medium-risk"></div>
                        <div class="tab-pane fade" id="low-risk"></div>
                    </div>
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

            function renderDrillDownModal(data) {
                if (typeof window.renderStudentTable !== 'function') {
                    console.warn('renderStudentTable is not available');
                    return;
                }
                var riskTabsWrap = document.getElementById('lr-risk-tabs-wrap');
                var highPane = document.getElementById('high-risk');
                var mediumPane = document.getElementById('medium-risk');
                var lowPane = document.getElementById('low-risk');
                var drillWrap = document.getElementById('lr-drilldown-wrap');
                var drillEmpty = document.getElementById('lr-drilldown-empty');

                function normalizeRiskRows(payload) {
                    var buckets = { high: [], medium: [], low: [] };
                    if (!payload || typeof payload !== 'object') {
                        return buckets;
                    }

                    if (Array.isArray(payload.groups) && payload.groups.length > 0) {
                        payload.groups.forEach(function (group) {
                            var heading = String((group && group.heading) || '').toLowerCase();
                            var rows = Array.isArray(group && group.rows) ? group.rows : [];
                            if (heading.indexOf('high') !== -1) {
                                buckets.high = rows;
                            } else if (heading.indexOf('medium') !== -1) {
                                buckets.medium = rows;
                            } else if (heading.indexOf('low') !== -1) {
                                buckets.low = rows;
                            }
                        });
                    }

                    if (Array.isArray(payload.rows) && payload.rows.length > 0) {
                        var t = String(payload.title || '').toLowerCase();
                        if (t.indexOf('high') !== -1) {
                            buckets.high = payload.rows;
                        } else if (t.indexOf('medium') !== -1) {
                            buckets.medium = payload.rows;
                        } else {
                            buckets.low = payload.rows;
                        }
                    }

                    return buckets;
                }

                function isRiskPayload(payload) {
                    if (!payload || typeof payload !== 'object') {
                        return false;
                    }
                    var title = String(payload.title || '').toLowerCase();
                    var groupHit = Array.isArray(payload.groups) && payload.groups.some(function (group) {
                        return String((group && group.heading) || '').toLowerCase().indexOf('risk') !== -1;
                    });
                    return title.indexOf('risk') !== -1 || groupHit;
                }

                function renderRowsTable(rows) {
                    if (!Array.isArray(rows) || rows.length === 0) {
                        return '<p class="text-white-50 small mb-0">No rows for this segment.</p>';
                    }

                    var keys = Object.keys(rows[0] || {});
                    if (keys.length === 0) {
                        return '<p class="text-white-50 small mb-0">No rows for this segment.</p>';
                    }

                    var thead = '<tr>' + keys.map(function (k) {
                        return '<th style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">' + escapeHtml(k.replace(/_/g, ' ')) + '</th>';
                    }).join('') + '</tr>';

                    var tbody = rows.map(function (row) {
                        return '<tr class="report-drilldown-row">' + keys.map(function (k) {
                            var val = row && row[k] != null ? String(row[k]) : '';
                            return '<td style="text-align:left;padding:12px 14px;vertical-align:middle;">' + escapeHtml(val) + '</td>';
                        }).join('') + '</tr>';
                    }).join('');

                    return '<div style="overflow-x:auto;"><table class="report-drilldown-table" style="font-size:0.95rem;"><thead>' + thead + '</thead><tbody>' + tbody + '</tbody></table></div>';
                }

                if (isRiskPayload(data)) {
                    var buckets = normalizeRiskRows(data);
                    riskTabsWrap.style.display = 'block';
                    drillWrap.style.display = 'none';
                    drillEmpty.style.display = 'none';

                    highPane.innerHTML = renderRowsTable(buckets.high);
                    mediumPane.innerHTML = renderRowsTable(buckets.medium);
                    lowPane.innerHTML = renderRowsTable(buckets.low);

                    document.querySelectorAll('#riskTabs .nav-link').forEach(function (btn) {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });
                    var highBtn = document.querySelector('#riskTabs .nav-link[data-bs-target="#high-risk"]');
                    if (highBtn) {
                        highBtn.classList.add('active');
                        highBtn.setAttribute('aria-selected', 'true');
                    }
                    highPane.classList.add('show', 'active');
                    mediumPane.classList.remove('show', 'active');
                    lowPane.classList.remove('show', 'active');

                    document.getElementById('lr-modal-title').textContent = data && data.title ? data.title : 'Risk Details';
                    document.getElementById('lr-modal-backdrop').style.display = 'flex';
                    return;
                }

                riskTabsWrap.style.display = 'none';
                window.renderStudentTable(data, {
                    title: document.getElementById('lr-modal-title'),
                    empty: drillEmpty,
                    wrap: drillWrap,
                    singleTableWrap: document.getElementById('lr-drilldown-single-wrap'),
                    groupedHost: document.getElementById('lr-drilldown-grouped-host'),
                    theadRow: document.getElementById('lr-drilldown-thead-row'),
                    tbody: document.getElementById('lr-drilldown-tbody'),
                    table: document.getElementById('lr-drilldown-table')
                });
                document.getElementById('lr-modal-backdrop').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('lr-modal-backdrop').style.display = 'none';
            }

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

                lrCharts.health = new ApexCharts(document.querySelector('#engagement-health'), window.hhApplyApexDefaults(options));
                lrCharts.health.render();
            }

            function renderHeatmap(data) {
                var cats = (data.trend && data.trend.categories) ? data.trend.categories : [];
                var seriesPayload = (data.trend && data.trend.series) ? data.trend.series : [];
                var sourceValues;
                if (
                    Array.isArray(seriesPayload) &&
                    seriesPayload.length > 0 &&
                    typeof seriesPayload[0] === 'object' &&
                    seriesPayload[0] !== null &&
                    Array.isArray(seriesPayload[0].data)
                ) {
                    sourceValues = seriesPayload[0].data.map(function (v) { return Number(v) || 0; });
                } else {
                    sourceValues = (seriesPayload || []).map(function (v) { return Number(v) || 0; });
                }

                var weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
                var totalsByDay = { Mon: 0, Tue: 0, Wed: 0, Thu: 0, Fri: 0 };
                cats.forEach(function (rawDate, i) {
                    var value = sourceValues[i] || 0;
                    var dt = new Date(String(rawDate));
                    if (isNaN(dt.getTime())) {
                        return;
                    }
                    var dayIndex = dt.getDay();
                    var key = null;
                    if (dayIndex === 1) key = 'Mon';
                    if (dayIndex === 2) key = 'Tue';
                    if (dayIndex === 3) key = 'Wed';
                    if (dayIndex === 4) key = 'Thu';
                    if (dayIndex === 5) key = 'Fri';
                    if (key) {
                        totalsByDay[key] += value;
                    }
                });

                var weekdayValues = weekdays.map(function (d) { return Number(totalsByDay[d] || 0); });
                var maxVal = Math.max.apply(null, weekdayValues.concat([0]));
                var step = Math.max(1, Math.ceil(maxVal / 4));
                var textColorThreshold = Math.max(2, Math.floor(maxVal * 0.45));

                var options = {
                    series: [{
                        name: 'Activity',
                        data: weekdays.map(function (d) {
                            return { x: d, y: totalsByDay[d] || 0 };
                        })
                    }],
                    dataLabels: {
                        enabled: true,
                        formatter: function (val) {
                            return String(Math.round(Number(val) || 0));
                        },
                        style: {
                            fontSize: '12px',
                            fontWeight: 700,
                            colors: ['#f8fafc']
                        }
                    },
                    xaxis: {
                        categories: weekdays,
                        type: 'category',
                        labels: { rotate: 0, style: { fontWeight: 700 } }
                    },
                    plotOptions: {
                        heatmap: {
                            radius: 6,
                            shadeIntensity: 0.65,
                            enableShades: true,
                            colorScale: {
                                inverse: false,
                                ranges: [
                                    { from: 0, to: step - 1, color: '#0f172a', foreColor: '#e2e8f0' },
                                    { from: step, to: (step * 2) - 1, color: '#1d4ed8', foreColor: '#f8fafc' },
                                    { from: step * 2, to: (step * 3) - 1, color: '#0ea5e9', foreColor: '#020617' },
                                    { from: step * 3, to: Math.max(step * 4, maxVal), color: '#22c55e', foreColor: '#020617' }
                                ]
                            }
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (val) {
                                return String(val) + ' points';
                            }
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'darken',
                                value: 0.15
                            }
                        }
                    },
                    chart: {
                        type: 'heatmap',
                        height: 320,
                        fontFamily: commonFont.fontFamily,
                        foreColor: commonFont.foreColor,
                        toolbar: { show: false },
                        events: {
                            mounted: function (chartContext) {
                                var labels = chartContext && chartContext.el
                                    ? chartContext.el.querySelectorAll('.apexcharts-datalabel text')
                                    : [];
                                labels.forEach(function (el, i) {
                                    var v = weekdayValues[i] || 0;
                                    el.style.fill = v >= textColorThreshold ? '#020617' : '#f8fafc';
                                });
                            },
                            updated: function (chartContext) {
                                var labels = chartContext && chartContext.el
                                    ? chartContext.el.querySelectorAll('.apexcharts-datalabel text')
                                    : [];
                                labels.forEach(function (el, i) {
                                    var v = weekdayValues[i] || 0;
                                    el.style.fill = v >= textColorThreshold ? '#020617' : '#f8fafc';
                                });
                            }
                        }
                    },
                    grid: { borderColor: '#334155' }
                };

                lrCharts.heatmap = new ApexCharts(document.querySelector('#heatmap'), window.hhApplyApexDefaults(options));
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
                    markers: { size: 4 },
                    dataLabels: { enabled: false },
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

                lrCharts.yearTrend = new ApexCharts(document.querySelector('#year-level'), window.hhApplyApexDefaults(options));
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
                                    var s = String(lbl);
                                    var sl = s.toLowerCase();
                                    if (sl === 'medium risk' || sl === 'high risk') {
                                        drillDown({ type: 'risk_segment_combined' });
                                    } else {
                                        drillDown({ type: 'risk_segment', value: s });
                                    }
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: { colors: ['#0f172a'], width: 2 },
                    fill: { opacity: 0.85 },
                    colors: ['#ef4444', '#f59e0b', '#22c55e'],
                    legend: {
                        show: true,
                        position: 'bottom',
                        fontSize: '14px',
                        labels: {
                            colors: '#e2e8f0'
                        },
                        formatter: function (seriesName, opts) {
                            var val = opts.w.globals.series[opts.seriesIndex];
                            var total = opts.w.globals.series.reduce(function (a, b) { return a + b; }, 0);
                            var pct = total ? ((val / total) * 100).toFixed(1) : 0;
                            return seriesName + ' (' + pct + '%)';
                        }
                    },
                    tooltip: { theme: 'dark' },
                    yaxis: { show: false }
                };

                lrCharts.distribution = new ApexCharts(document.querySelector('#risk-mix'), window.hhApplyApexDefaults(options));
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

                lrCharts.dropoff = new ApexCharts(document.querySelector('#lr-dropoff'), window.hhApplyApexDefaults(options));
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

