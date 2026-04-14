<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HouseHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            color: white;
        }

        /* NAVBAR (hh- prefix avoids clashing with Bootstrap .navbar) */
        .hh-navbar {
            width: 100%;
            background: #1e293b;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            box-sizing: border-box;
        }

        .nav-left {
            font-size: 18px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-right > a {
            color: white;
            text-decoration: none;
        }

        .nav-right > a:hover {
            text-decoration: underline;
        }

        .nav-right > a.active {
            text-decoration: underline;
            border-bottom: 2px solid #fff;
            padding-bottom: 2px;
        }

        .nav-right .dropdown-toggle {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 0;
        }

        .nav-right .dropdown-toggle:hover,
        .nav-right .dropdown-toggle:focus {
            color: #fff;
            text-decoration: underline;
        }

        .nav-right .dropdown-toggle.active {
            text-decoration: underline;
            border-bottom: 2px solid #fff;
            padding-bottom: 2px;
        }

        .nav-right .dropdown-toggle::after {
            margin-left: 0.35em;
            vertical-align: 0.15em;
        }

        .nav-right .dropdown-menu {
            background: #1e293b;
            border: 1px solid #334155;
            margin-top: 0.5rem;
            padding: 0.35rem 0;
        }

        .nav-right .dropdown-item {
            color: #f1f5f9;
            font-weight: normal;
            padding: 0.45rem 1rem;
        }

        .nav-right .dropdown-item:hover,
        .nav-right .dropdown-item:focus {
            background: #334155;
            color: #fff;
        }

        .nav-right .dropdown-item.active,
        .nav-right .dropdown-item:active {
            background: #475569;
            color: #fff;
        }

        /* PAGE CONTENT */
        .page-content {
            padding: 20px;
        }

        .page-content .card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border: none;
            border-radius: 12px;
            color: #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
        }

        .page-content .card:hover {
            transform: translateY(-2px);
        }

        .page-content .card h5 {
            color: #f8fafc;
            font-weight: 600;
        }

        .page-content .card p,
        .page-content .card .text-muted,
        .page-content .card .small {
            color: #94a3b8 !important;
        }

        .hh-card {
            background: linear-gradient(145deg, #1e293b, #0f172a) !important;
            border: none !important;
            border-radius: 12px;
            color: #e2e8f0 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .hh-card h5 {
            color: #f8fafc !important;
            font-weight: 600;
        }

        .hh-card p,
        .hh-card .text-muted,
        .hh-card .small {
            color: #94a3b8 !important;
        }

        .hh-card .card-body {
            background: transparent !important;
        }

        .student-link {
            color: #93c5fd;
            text-decoration: none;
            font-weight: 600;
        }

        .student-link:hover {
            color: #bfdbfe;
            text-decoration: underline;
        }

        .modal-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #1e293b;
            border-bottom: 1px solid #334155;
        }

        table.report-drilldown-table {
            border-collapse: collapse;
            width: 100%;
        }

        table.report-drilldown-table th[data-sort-key] {
            cursor: pointer;
            user-select: none;
        }

        table.report-drilldown-table th[data-sort-key]:hover {
            color: #f8fafc;
        }

        table.report-drilldown-table tbody tr.report-drilldown-row:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        table.report-drilldown-table td {
            padding: 12px 14px;
            vertical-align: middle;
        }

        table.report-drilldown-table td.td-name,
        table.report-drilldown-table td.td-name .student-link {
            color: #f8fafc;
        }

        table.report-drilldown-table td.td-actions {
            text-align: right;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="hh-navbar">
        <div class="nav-left">🏫 HouseHub</div>

        <div class="nav-right">
            <a href="/points">Points</a>

            <div class="dropdown">
                <a class="dropdown-toggle {{ request()->routeIs('reports.pc', 'reports.leadership', 'reports.teachers', 'reports.house', 'reports.houses') ? 'active' : '' }}"
                   href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Reports
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="reportsDropdown">
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.house') ? 'active' : '' }}"
                           href="{{ route('reports.house') }}">House performance</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.pc') ? 'active' : '' }}"
                           href="{{ route('reports.pc') }}">Pastoral Insights</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.leadership') ? 'active' : '' }}"
                           href="{{ route('reports.leadership') }}">Leadership Overview</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.teachers') ? 'active' : '' }}"
                           href="{{ route('reports.teachers') }}">Staff Engagement</a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->routeIs('reports.houses') ? 'active' : '' }}"
                           href="{{ route('reports.houses') }}">House Performance</a>
                    </li>
                </ul>
            </div>

            <a href="/tv">TV</a>
            <a href="/admin">Admin</a>
        </div>
    </div>

    {{-- @yield for @extends; $slot for <x-app-layout> (both may be empty; only one is used) --}}
    <div class="page-content">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts failed to load');
        }
        window.formatReportChartDate = function (d) {
            if (d == null || d === '') return '';
            var s = String(d).trim();
            var m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
            if (m) {
                return m[3] + '/' + m[2];
            }
            var dt = new Date(s.indexOf('T') === -1 ? s + 'T00:00:00' : s);
            if (!isNaN(dt.getTime())) {
                var dd = String(dt.getDate()).padStart(2, '0');
                var mm = String(dt.getMonth() + 1).padStart(2, '0');
                return dd + '/' + mm;
            }
            return s;
        };

        (function () {
            window._reportDrilldownSortState = window._reportDrilldownSortState || new Map();

            function escapeReportHtml(value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function mapDrilldownRowToStandard(raw) {
                var r = raw && typeof raw === 'object' ? raw : {};
                var sid = r.id != null ? r.id : (r._studentId != null ? r._studentId : (r.student_id != null ? r.student_id : null));

                var name = '';
                if (r.name != null && String(r.name).trim() !== '') {
                    name = String(r.name).trim();
                } else if (r.first_name != null || r.last_name != null) {
                    name = (String(r.first_name || '') + ' ' + String(r.last_name || '')).trim();
                } else if (r.teacher != null) {
                    name = String(r.teacher);
                } else if (r.student != null) {
                    name = String(r.student);
                } else if (r.house_name != null) {
                    name = String(r.house_name);
                } else {
                    name = '—';
                }

                var yl = r.year_level != null ? String(r.year_level) : '';

                var act = r.activity_count != null ? r.activity_count
                    : (r.weekday_awards != null ? r.weekday_awards
                        : (r.total_actions != null ? r.total_actions
                            : (r.total_points != null ? r.total_points
                                : (r.amount != null ? r.amount
                                    : (r.points_in_range != null ? r.points_in_range
                                        : (r.total != null ? r.total
                                            : (r['points (weekdays)'] != null ? r['points (weekdays)'] : '')))))));
                if (act !== null && act !== undefined && typeof act !== 'string') {
                    act = String(act);
                }

                var dateRaw = r.created_at != null ? r.created_at : (r.date != null ? r.date : null);
                var dateStr = '';
                if (dateRaw != null && dateRaw !== '') {
                    var ds = String(dateRaw);
                    var slice = ds.length >= 10 ? ds.slice(0, 10) : ds;
                    dateStr = typeof window.formatReportChartDate === 'function' ? window.formatReportChartDate(slice) : slice;
                }

                return {
                    _studentId: sid,
                    name: name || '—',
                    year_level: yl,
                    activity_count: act === null || act === undefined ? '' : String(act),
                    date: dateStr
                };
            }

            function reportShowToast(message) {
                var el = document.getElementById('report-global-toast');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'report-global-toast';
                    el.style.cssText = 'position:fixed;right:20px;bottom:20px;background:#0f172a;color:#e2e8f0;border:1px solid #334155;padding:10px 14px;border-radius:8px;z-index:1200;display:none;';
                    document.body.appendChild(el);
                }
                el.textContent = message;
                el.style.display = 'block';
                setTimeout(function () { el.style.display = 'none'; }, 1500);
            }

            function reportSendPoint(studentId, amount) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch('/points', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ student_id: studentId, amount: amount })
                })
                    .then(function (res) { return res.json(); })
                    .then(function () { reportShowToast('Points updated'); })
                    .catch(function () { reportShowToast('Unable to update points'); });
            }

            function renderStandardDrilldownBody(tbody, rows) {
                tbody.innerHTML = rows.map(function (row) {
                    var sid = row._studentId;
                    var nm = escapeReportHtml(row.name || '');
                    var yl = escapeReportHtml(row.year_level || '');
                    var ac = escapeReportHtml(row.activity_count || '');
                    var dt = escapeReportHtml(row.date || '');
                    var nameCell = sid != null
                        ? '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;"><a href="/students/' + encodeURIComponent(String(sid)) + '" class="student-link">' + nm + '</a></td>'
                        : '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;">' + nm + '</td>';
                    var dis = sid == null ? ' disabled' : '';
                    var dataId = sid != null ? escapeReportHtml(String(sid)) : '';
                    var actions =
                        '<td class="td-actions" style="text-align:right;padding:12px 14px;vertical-align:middle;">' +
                        '<div class="d-flex gap-2 justify-content-end flex-wrap">' +
                        '<button type="button" class="btn-sub btn btn-sm"' + dis + ' data-id="' + dataId + '">-1</button>' +
                        '<button type="button" class="btn-add btn btn-sm"' + dis + ' data-id="' + dataId + '">+1</button>' +
                        '<button type="button" class="btn-award btn btn-sm"' + dis + ' data-id="' + dataId + '">🏆</button>' +
                        '<button type="button" class="btn-commend btn btn-sm"' + dis + ' data-id="' + dataId + '">⭐</button>' +
                        '</div></td>';
                    return '<tr class="report-drilldown-row">' + nameCell +
                        '<td style="text-align:left;padding:12px 14px;vertical-align:middle;">' + yl + '</td>' +
                        '<td style="text-align:left;padding:12px 14px;vertical-align:middle;">' + ac + '</td>' +
                        '<td style="text-align:left;padding:12px 14px;vertical-align:middle;">' + dt + '</td>' +
                        actions +
                        '</tr>';
                }).join('');
            }

            function sortStandardRows(rows, key, currentSort) {
                if (currentSort.key === key) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.key = key;
                    currentSort.direction = 'asc';
                }
                return rows.slice().sort(function (a, b) {
                    var valA = a[key];
                    var valB = b[key];
                    if (valA == null) valA = '';
                    if (valB == null) valB = '';
                    if (typeof valA === 'string') valA = valA.toLowerCase();
                    if (typeof valB === 'string') valB = valB.toLowerCase();
                    var na = Number(valA);
                    var nb = Number(valB);
                    if (!isNaN(na) && !isNaN(nb) && String(valA).trim() !== '' && String(valB).trim() !== '') {
                        return currentSort.direction === 'asc' ? na - nb : nb - na;
                    }
                    if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
                    if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            /**
             * @param data {{ title?: string, rows?: array, student_breakdown?: array }}
             * @param els {{ title: Element, empty: Element, wrap: Element, theadRow: Element, tbody: Element, table: Element }}
             */
            window.renderStudentTable = function (data, els) {
                var title = (data && data.title) ? data.title : 'Details';
                var rawRows = (data && data.rows) ? data.rows : ((data && data.student_breakdown) ? data.student_breakdown : []);
                if (els.title) {
                    els.title.textContent = title;
                }
                if (!rawRows.length) {
                    window._reportDrilldownSortState.delete(els.table.id);
                    if (els.empty) els.empty.style.display = 'block';
                    if (els.wrap) els.wrap.style.display = 'none';
                    els.theadRow.innerHTML = '';
                    els.tbody.innerHTML = '';
                    return;
                }
                if (els.empty) els.empty.style.display = 'none';
                if (els.wrap) {
                    els.wrap.style.display = 'block';
                    els.wrap.classList.add('hh-card', 'p-3');
                }
                var mapped = rawRows.map(mapDrilldownRowToStandard);
                var currentSort = { key: null, direction: 'asc' };
                window._reportDrilldownSortState.set(els.table.id, { rows: mapped, currentSort: currentSort, els: els });

                els.theadRow.innerHTML =
                    '<th data-sort-key="name" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Name</th>' +
                    '<th data-sort-key="year_level" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Year Level</th>' +
                    '<th data-sort-key="activity_count" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Activity</th>' +
                    '<th data-sort-key="date" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Date</th>' +
                    '<th style="text-align:right;padding:12px 14px;border-bottom:2px solid #334155;">Actions</th>';

                renderStandardDrilldownBody(els.tbody, mapped);
            };

            document.addEventListener('click', function (e) {
                var th = e.target.closest('th.report-sort-th[data-sort-key]');
                if (!th) return;
                var table = th.closest('table.report-drilldown-table');
                if (!table || !table.id) return;
                var state = window._reportDrilldownSortState.get(table.id);
                if (!state || !state.rows.length) return;
                var key = th.getAttribute('data-sort-key');
                if (!key) return;
                var sorted = sortStandardRows(state.rows, key, state.currentSort);
                state.rows = sorted;
                renderStandardDrilldownBody(state.els.tbody, sorted);
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-add')) {
                    var id = e.target.getAttribute('data-id');
                    if (id) reportSendPoint(id, 1);
                }
                if (e.target.classList.contains('btn-sub')) {
                    var id2 = e.target.getAttribute('data-id');
                    if (id2) reportSendPoint(id2, -1);
                }
                if (e.target.classList.contains('btn-award')) {
                    var id3 = e.target.getAttribute('data-id');
                    if (id3 && typeof window.openAwardModal === 'function') {
                        window.openAwardModal(id3);
                    } else if (id3) {
                        window.dispatchEvent(new CustomEvent('award:open', { detail: { student_id: id3 } }));
                    }
                }
                if (e.target.classList.contains('btn-commend')) {
                    var id4 = e.target.getAttribute('data-id');
                    if (id4 && typeof window.openCommendationModal === 'function') {
                        window.openCommendationModal(id4);
                    } else if (id4) {
                        window.dispatchEvent(new CustomEvent('commendation:open', { detail: { student_id: id4 } }));
                    }
                }
            });
        })();

        window.Apex = {
            colors: ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#a855f7'],
            chart: {
                background: 'transparent',
                toolbar: {
                    show: false
                }
            },
            tooltip: {
                theme: 'dark',
                fillSeriesColor: false,
                style: {
                    fontSize: '13px',
                    fontFamily: 'inherit'
                }
            },
            grid: {
                borderColor: '#334155',
                strokeDashArray: 4
            },
            xaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontSize: '12px'
                    }
                }
            },
            legend: {
                labels: {
                    colors: '#cbd5f5'
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#f8fafc'],
                    fontWeight: 600
                }
            },
            states: {
                hover: {
                    filter: {
                        type: 'lighten',
                        value: 0.1
                    }
                }
            },
        };
    </script>
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
