@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">Teacher Usage Report</h1>
    <p style="font-size: 1.05rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 52rem;">
        Staff-focused view: charts emphasise low usage. Drill-downs use the same filters as other reports (no full page reload).
    </p>

    <div id="tu-filter-bar" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; margin-bottom: 1.75rem; padding: 16px; background: #1e293b; border-radius: 8px;">
        <div>
            <label for="tu-house" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">House</label>
            <select id="tu-house" style="min-width: 160px; padding: 8px 10px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                @foreach ($houses as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="tu-start" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Start</label>
            <input type="date" id="tu-start" style="padding: 8px 10px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="tu-end" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">End</label>
            <input type="date" id="tu-end" style="padding: 8px 10px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="tu-year" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Year</label>
            <select id="tu-year" style="min-width: 120px; padding: 8px 10px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                @foreach (['7', '8', '9', '10', '11', '12'] as $y)
                    <option value="{{ $y }}">Year {{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="button" id="tu-apply" style="padding: 10px 20px; font-size: 1rem; font-weight: 600; border: none; border-radius: 6px; background: #3b82f6; color: #fff; cursor: pointer;">Apply</button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Student Distribution by Teacher</h5>
                    <p class="text-muted small mb-3">
                        Shows which students receive the highest share of points from each teacher. Use this to identify concentration and potential award bias.
                    </p>
                    <div id="teacher-bias" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Staff Usage Frequency</h5>
                    <p class="text-muted small mb-3">
                        Groups staff by how often they award points in the selected period. Lower buckets highlight underuse and support opportunities.
                    </p>
                    <div id="teacher-frequency" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Weekday Points Trend</h5>
                    <p class="text-muted small mb-3">
                        Displays overall staff point activity across weekdays. Sudden dips help pinpoint low-engagement days for follow-up.
                    </p>
                    <div id="tu-trend" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="tu-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div id="tu-modal" role="dialog" aria-modal="true" style="background: #1e293b; color: #f1f5f9; max-width: 900px; width: 100%; max-height: 85vh; overflow: auto; border-radius: 10px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-bottom: 1px solid #334155;">
                <h3 id="tu-modal-title" style="margin: 0; font-size: 1.15rem;">Details</h3>
                <button type="button" id="tu-modal-close" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; line-height: 1; cursor: pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="tu-modal-body" style="padding: 16px 18px;">
                <div id="tu-drilldown-chart-wrap" style="display: none; margin-bottom: 20px;">
                    <div id="drilldown-chart" style="min-height: 280px;"></div>
                </div>
                <p id="tu-empty" style="opacity:0.9;margin:0;display:none;">No rows.</p>
                <div id="tu-table-wrap" style="display:none; overflow-x: auto;">
                    <table id="tu-table" style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                        <thead><tr id="tu-thead"></tr></thead>
                        <tbody id="tu-tbody"></tbody>
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

            var tuCharts = { low: null, freq: null, trend: null };
            var tuModalChart = null;
            var tuTrendRawDates = [];

            var filters = {
                house: 'All',
                start_date: null,
                end_date: null,
                year: 'All'
            };

            function ymd(d) {
                var y = d.getFullYear();
                var m = String(d.getMonth() + 1).padStart(2, '0');
                var day = String(d.getDate()).padStart(2, '0');
                return y + '-' + m + '-' + day;
            }

            var end = new Date();
            var start = new Date();
            start.setDate(start.getDate() - 29);
            filters.start_date = ymd(start);
            filters.end_date = ymd(end);

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
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
                    .then(function (r) {
                        return r.json();
                    })
                    .then(renderTuModal)
                    .catch(function () {});
            }

            function destroyTuModalChart() {
                if (tuModalChart) {
                    tuModalChart.destroy();
                    tuModalChart = null;
                }
                var chartHost = document.getElementById('drilldown-chart');
                if (chartHost) {
                    chartHost.innerHTML = '';
                }
            }

            function renderTuModal(data) {
                console.log('DRILLDOWN DATA:', data);

                var title = data.title || 'Details';
                var emptyEl = document.getElementById('tu-empty');
                var wrap = document.getElementById('tu-table-wrap');
                var thead = document.getElementById('tu-thead');
                var tbody = document.getElementById('tu-tbody');
                var chartWrap = document.getElementById('tu-drilldown-chart-wrap');

                if (data.student_breakdown && data.student_breakdown.length > 0) {
                    destroyTuModalChart();
                    document.getElementById('tu-modal-title').textContent = title;
                    chartWrap.style.display = 'block';

                    var students = data.student_breakdown;
                    var names = students.map(function (s) {
                        return ((s.first_name || '') + ' ' + (s.last_name || '')).trim();
                    });
                    var values = students.map(function (s) {
                        return parseInt(s.total_points, 10) || 0;
                    });

                    var container = document.getElementById('drilldown-chart');
                    if (container) {
                        container.innerHTML = '';
                    }

                    var common = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };
                    var options = {
                        chart: {
                            type: 'bar',
                            height: 320,
                            fontFamily: common.fontFamily,
                            foreColor: common.foreColor,
                            toolbar: { show: false }
                        },
                        series: [{ name: 'Points', data: values }],
                        xaxis: {
                            categories: names,
                            labels: { rotate: -35, style: { fontSize: '11px' } }
                        },
                        colors: ['#3b82f6'],
                        plotOptions: { bar: { borderRadius: 4, columnWidth: '65%' } },
                        grid: { borderColor: '#334155' },
                        dataLabels: { enabled: true },
                        tooltip: { theme: 'dark' }
                    };

                    tuModalChart = new ApexCharts(document.querySelector('#drilldown-chart'), options);
                    tuModalChart.render();

                    emptyEl.style.display = 'none';
                    wrap.style.display = 'block';
                    thead.innerHTML =
                        '<th style="text-align:left;padding:8px 10px;border-bottom:2px solid #334155;">Student</th>' +
                        '<th style="text-align:right;padding:8px 10px;border-bottom:2px solid #334155;">Total points</th>';
                    tbody.innerHTML = students
                        .map(function (s) {
                            var nm = ((s.first_name || '') + ' ' + (s.last_name || '')).trim() || '—';
                            var pts = parseInt(s.total_points, 10) || 0;
                            return (
                                '<tr style="border-bottom:1px solid #334155;">' +
                                '<td style="padding:8px 10px;">' + escapeHtml(nm) + '</td>' +
                                '<td style="padding:8px 10px;text-align:right;">' + escapeHtml(String(pts)) + '</td>' +
                                '</tr>'
                            );
                        })
                        .join('');

                    document.getElementById('tu-modal-backdrop').style.display = 'flex';
                    return;
                }

                if (data.student_breakdown) {
                    console.warn('No student breakdown data found');
                    destroyTuModalChart();
                    chartWrap.style.display = 'none';
                    document.getElementById('tu-modal-title').textContent = title;
                    emptyEl.textContent = 'No student point data for this staff member in the selected range.';
                    emptyEl.style.display = 'block';
                    wrap.style.display = 'none';
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
                    document.getElementById('tu-modal-backdrop').style.display = 'flex';
                    return;
                }

                destroyTuModalChart();
                chartWrap.style.display = 'none';

                var rows = data.rows || [];
                document.getElementById('tu-modal-title').textContent = title;
                if (!rows.length) {
                    emptyEl.textContent = 'No rows.';
                    emptyEl.style.display = 'block';
                    wrap.style.display = 'none';
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
                } else {
                    emptyEl.style.display = 'none';
                    wrap.style.display = 'block';
                    var th = ' style="text-align:left;padding:8px 10px;border-bottom:2px solid #334155;"';
                    var thR = ' style="text-align:right;padding:8px 10px;border-bottom:2px solid #334155;"';
                    var isTeacherBucket =
                        rows.length &&
                        Object.prototype.hasOwnProperty.call(rows[0], 'teacher') &&
                        Object.prototype.hasOwnProperty.call(rows[0], 'total_actions');
                    if (isTeacherBucket) {
                        thead.innerHTML =
                            '<th' + th + '>Teacher</th>' +
                            '<th' + thR + '>Awards in range</th>';
                        tbody.innerHTML = rows
                            .map(function (row) {
                                return (
                                    '<tr style="border-bottom:1px solid #334155;">' +
                                    '<td style="padding:8px 10px;">' +
                                    escapeHtml(row.teacher == null ? '' : String(row.teacher)) +
                                    '</td>' +
                                    '<td style="padding:8px 10px;text-align:right;">' +
                                    escapeHtml(String(parseInt(row.total_actions, 10) || 0)) +
                                    '</td>' +
                                    '</tr>'
                                );
                            })
                            .join('');
                    } else {
                        var keys = Object.keys(rows[0]);
                        thead.innerHTML = keys.map(function (k) {
                            return '<th' + th + '>' + escapeHtml(k) + '</th>';
                        }).join('');
                        tbody.innerHTML = rows.map(function (row) {
                            return (
                                '<tr style="border-bottom:1px solid #334155;">' +
                                keys
                                    .map(function (k) {
                                        var v = row[k];
                                        return '<td style="padding:8px 10px;">' + escapeHtml(v == null ? '' : String(v)) + '</td>';
                                    })
                                    .join('') +
                                '</tr>'
                            );
                        }).join('');
                    }
                }
                document.getElementById('tu-modal-backdrop').style.display = 'flex';
            }

            function destroyTuCharts() {
                ['low', 'freq', 'trend'].forEach(function (k) {
                    if (tuCharts[k]) {
                        tuCharts[k].destroy();
                        tuCharts[k] = null;
                    }
                });
            }

            function renderLowUsage(data) {
                var recent = data.recent || [];
                var teacherCounts = {};
                recent.forEach(function (row) {
                    var teacher = row.teacher || 'Unknown';
                    teacherCounts[teacher] = (teacherCounts[teacher] || 0) + 1;
                });
                var teachers = Object.keys(teacherCounts).map(function (t) {
                    return { name: t, count: teacherCounts[t] };
                });
                teachers.sort(function (a, b) {
                    return b.count - a.count;
                });
                var top = teachers.slice(0, 10);
                if (!top.length) {
                    top.push({ name: 'No data', count: 0 });
                }

                var common = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };
                tuCharts.low = new ApexCharts(document.querySelector('#teacher-bias'), {
                    chart: {
                        type: 'bar',
                        height: 320,
                        fontFamily: common.fontFamily,
                        foreColor: common.foreColor,
                        toolbar: { show: false },
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
                                var row = top[config.dataPointIndex];
                                if (!row || row.name === 'No data') {
                                    return;
                                }
                                drillDown({ type: 'teacher', value: row.name });
                            }
                        }
                    },
                    title: {
                        text: 'Top 10 Staff Usage',
                        align: 'left',
                        style: { color: '#e2e8f0', fontSize: '16px', fontWeight: 600 }
                    },
                    series: [{ name: 'Awards', data: top.map(function (t) { return t.count; }) }],
                    xaxis: {
                        categories: top.map(function (t) { return t.name; }),
                        labels: { rotate: -35, style: { fontSize: '12px' } }
                    },
                    yaxis: { min: 0, labels: { style: { fontSize: '13px' } } },
                    colors: ['#f97316'],
                    plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                    grid: { borderColor: '#334155' },
                    dataLabels: { enabled: true },
                    tooltip: { theme: 'dark' }
                });
                tuCharts.low.render();
            }

            function renderFrequency(data) {
                var recent = data.recent || [];
                var teacherCounts = {};
                recent.forEach(function (row) {
                    var teacher = row.teacher || 'Unknown';
                    teacherCounts[teacher] = (teacherCounts[teacher] || 0) + 1;
                });
                var buckets = {
                    '0-5': [],
                    '6-10': [],
                    '11-20': [],
                    '20+': []
                };
                Object.keys(teacherCounts).forEach(function (t) {
                    var count = teacherCounts[t];
                    if (count <= 5) {
                        buckets['0-5'].push(t);
                    } else if (count <= 10) {
                        buckets['6-10'].push(t);
                    } else if (count <= 20) {
                        buckets['11-20'].push(t);
                    } else {
                        buckets['20+'].push(t);
                    }
                });
                Object.keys(buckets).forEach(function (k) {
                    buckets[k].sort();
                });
                var bucketKeys = Object.keys(buckets);
                var bucketCounts = bucketKeys.map(function (k) {
                    return buckets[k].length;
                });
                var common = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };
                tuCharts.freq = new ApexCharts(document.querySelector('#teacher-frequency'), {
                    chart: {
                        type: 'bar',
                        height: 320,
                        fontFamily: common.fontFamily,
                        foreColor: common.foreColor,
                        toolbar: { show: false },
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
                                var selectedBucket = bucketKeys[config.dataPointIndex];
                                var teachersInBucket = buckets[selectedBucket] || [];
                                drillDown({ type: 'teacher_bucket', value: teachersInBucket });
                            }
                        }
                    },
                    series: [{ name: 'Teachers', data: bucketCounts }],
                    xaxis: { categories: bucketKeys, labels: { style: { fontSize: '13px' } } },
                    yaxis: { min: 0, labels: { style: { fontSize: '13px' } } },
                    colors: ['#64748b'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                    grid: { borderColor: '#334155' },
                    dataLabels: { enabled: true },
                    tooltip: { theme: 'dark' }
                });
                tuCharts.freq.render();
            }

            function renderTrend(data) {
                var trend = data.trend || { categories: [], series: [] };
                var rawDates = trend.categories || [];
                var seriesPayload = trend.series;
                var values;
                if (
                    Array.isArray(seriesPayload) &&
                    seriesPayload.length > 0 &&
                    typeof seriesPayload[0] === 'object' &&
                    seriesPayload[0] !== null &&
                    Array.isArray(seriesPayload[0].data)
                ) {
                    values = seriesPayload[0].data.map(function (v) { return Number(v) || 0; });
                } else {
                    values = (seriesPayload || []).map(function (v) { return Number(v) || 0; });
                }
                tuTrendRawDates = rawDates.slice();
                var displayDates = rawDates.map(function (d) {
                    var date = new Date(d + 'T00:00:00');
                    var day = date.getDate();
                    var month = date.toLocaleString('en-AU', { month: 'short' });
                    return `${day} ${month}`;
                });
                var minValue = values.length ? Math.min.apply(null, values) : 0;
                var worstIndex = values.length ? values.indexOf(minValue) : 0;
                var annotations = { points: [] };
                if (values.length && displayDates[worstIndex] != null) {
                    annotations.points = [
                        {
                            x: displayDates[worstIndex],
                            y: minValue,
                            seriesIndex: 0,
                            marker: { size: 8, fillColor: '#ff4d4f' },
                            label: {
                                borderColor: '#ff4d4f',
                                style: { color: '#fff', background: '#ff4d4f' },
                                text: 'Lowest Usage Day'
                            }
                        }
                    ];
                }
                var common = { fontFamily: 'Arial, sans-serif', foreColor: '#e2e8f0' };
                tuCharts.trend = new ApexCharts(document.querySelector('#tu-trend'), {
                    chart: {
                        type: 'line',
                        height: 320,
                        fontFamily: common.fontFamily,
                        foreColor: common.foreColor,
                        toolbar: { show: false },
                        zoom: { enabled: false },
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
                                var rawDate = tuTrendRawDates[config.dataPointIndex];
                                if (rawDate) {
                                    drillDown({ type: 'date', value: rawDate, scope: 'staff' });
                                }
                            }
                        }
                    },
                    series: [{ name: 'Points', data: values }],
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, hover: { size: 8 } },
                    xaxis: {
                        type: 'category',
                        categories: displayDates,
                        labels: { rotate: -45, style: { fontSize: '12px' } }
                    },
                    yaxis: { labels: { style: { fontSize: '13px' } } },
                    grid: { borderColor: '#334155' },
                    annotations: annotations,
                    tooltip: { theme: 'dark' }
                });
                tuCharts.trend.render();
            }

            function renderTeacherCharts(data) {
                try {
                    destroyTuCharts();
                    renderLowUsage(data);
                    renderFrequency(data);
                    renderTrend(data);
                } catch (e) {
                    console.error('Chart render failed:', e);
                }
            }

            function fetchTeacherData() {
                fetch(dataUrl + '?' + chartsQueryString(), {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        console.log('Chart data:', data);
                        renderTeacherCharts(data);
                    })
                    .catch(function () {});
            }

            function syncFiltersFromDom() {
                filters.house = document.getElementById('tu-house').value || 'All';
                filters.start_date = document.getElementById('tu-start').value || null;
                filters.end_date = document.getElementById('tu-end').value || null;
                filters.year = document.getElementById('tu-year').value || 'All';
            }

            function syncDomFromFilters() {
                document.getElementById('tu-house').value = filters.house;
                document.getElementById('tu-start').value = filters.start_date || '';
                document.getElementById('tu-end').value = filters.end_date || '';
                document.getElementById('tu-year').value = filters.year || 'All';
            }

            document.getElementById('tu-apply').addEventListener('click', function () {
                syncFiltersFromDom();
                fetchTeacherData();
            });
            document.getElementById('tu-modal-close').addEventListener('click', function () {
                destroyTuModalChart();
                document.getElementById('tu-modal-backdrop').style.display = 'none';
            });
            document.getElementById('tu-modal-backdrop').addEventListener('click', function (e) {
                if (e.target.id === 'tu-modal-backdrop') {
                    destroyTuModalChart();
                    document.getElementById('tu-modal-backdrop').style.display = 'none';
                }
            });

            syncDomFromFilters();
            fetchTeacherData();
        })();
    </script>
@endpush
