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
            if (s.indexOf('T') !== -1) {
                s = s.split('T')[0];
            } else if (s.indexOf(' ') !== -1) {
                s = s.split(' ')[0];
            }
            if (s.length > 10) {
                s = s.slice(0, 10);
            }
            var m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
            if (m) {
                return m[3] + '/' + m[2];
            }
            var dt = new Date(s.length === 10 ? s + 'T00:00:00' : s);
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

            function drilldownDateOnlyPart(dateRaw) {
                if (dateRaw == null || dateRaw === '') return '';
                var ds = String(dateRaw).trim();
                if (ds.indexOf('T') !== -1) {
                    ds = ds.split('T')[0];
                } else if (ds.indexOf(' ') !== -1) {
                    ds = ds.split(' ')[0];
                }
                return ds.length >= 10 ? ds.slice(0, 10) : ds;
            }

            function pickDrilldownMetric(r) {
                var checks = [
                    { key: 'activity_count', kind: 'activity' },
                    { key: 'weekday_awards', kind: 'activity' },
                    { key: 'total_actions', kind: 'activity' },
                    { key: 'total_points', kind: 'points' },
                    { key: 'house_points', kind: 'points' },
                    { key: 'amount', kind: 'points' },
                    { key: 'points_in_range', kind: 'points' },
                    { key: 'total', kind: 'points' },
                    { key: 'points (range)', kind: 'points' },
                    { key: 'points (weekdays)', kind: 'points' }
                ];
                for (var i = 0; i < checks.length; i++) {
                    var c = checks[i];
                    if (Object.prototype.hasOwnProperty.call(r, c.key) && r[c.key] != null) {
                        return { val: r[c.key], kind: c.kind };
                    }
                }
                return { val: '', kind: 'activity' };
            }

            function resolveDrilldownStudentId(r) {
                if (!r || typeof r !== 'object') {
                    return null;
                }
                var keys = ['student_id', 'studentId', '_studentId', 'id'];
                for (var ki = 0; ki < keys.length; ki++) {
                    var k = keys[ki];
                    if (!Object.prototype.hasOwnProperty.call(r, k)) {
                        continue;
                    }
                    var v = r[k];
                    if (v == null) {
                        continue;
                    }
                    var s = String(v).trim();
                    if (s === '' || s === 'undefined' || s.toLowerCase() === 'null') {
                        continue;
                    }
                    return v;
                }
                return null;
            }

            function mapDrilldownRowToStandard(raw) {
                var r = raw && typeof raw === 'object' ? raw : {};
                var sid = resolveDrilldownStudentId(r);

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

                var picked = pickDrilldownMetric(r);
                var act = picked.val;
                if (act !== null && act !== undefined && typeof act !== 'string') {
                    act = String(act);
                }

                var dateRaw = r.created_at != null ? r.created_at : (r.date != null ? r.date : null);
                var dateStr = '';
                if (dateRaw != null && dateRaw !== '') {
                    var slice = drilldownDateOnlyPart(dateRaw);
                    dateStr = typeof window.formatReportChartDate === 'function' ? window.formatReportChartDate(slice) : slice;
                }

                return {
                    student_id: sid,
                    _studentId: sid,
                    _metricKind: picked.kind,
                    name: name || '—',
                    year_level: yl,
                    activity_count: act === null || act === undefined ? '' : String(act),
                    date: dateStr
                };
            }

            function inferMetricColumnLabel(mappedRows) {
                var anyPoints = false;
                var anyActivity = false;
                for (var i = 0; i < mappedRows.length; i++) {
                    if (mappedRows[i]._metricKind === 'points') {
                        anyPoints = true;
                    } else {
                        anyActivity = true;
                    }
                }
                if (anyPoints && !anyActivity) {
                    return 'Points';
                }
                if (anyActivity && !anyPoints) {
                    return 'Activity';
                }
                if (anyPoints && anyActivity) {
                    return 'Points';
                }
                return 'Activity';
            }

            function buildDrilldownTheadHtml(metricLabel) {
                var ml = escapeReportHtml(metricLabel);
                return (
                    '<th data-sort-key="name" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Name</th>' +
                    '<th data-sort-key="year_level" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Year Level</th>' +
                    '<th data-sort-key="activity_count" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">' + ml + '</th>' +
                    '<th data-sort-key="date" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Date</th>' +
                    '<th class="text-end" style="padding:12px 14px;border-bottom:2px solid #334155;">Actions</th>'
                );
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

            function studentIdUsable(sid) {
                if (sid == null) {
                    return false;
                }
                var s = String(sid).trim();
                return s !== '' && s !== 'undefined' && s.toLowerCase() !== 'null';
            }

            function rowStudentId(row) {
                if (!row) {
                    return null;
                }
                if (studentIdUsable(row.student_id)) {
                    return row.student_id;
                }
                if (studentIdUsable(row._studentId)) {
                    return row._studentId;
                }
                return null;
            }

            function renderStandardDrilldownBody(tbody, rows) {
                tbody.innerHTML = rows.map(function (row) {
                    var sid = rowStudentId(row);
                    var nm = escapeReportHtml(row.name || '');
                    var yl = escapeReportHtml(row.year_level || '');
                    var ac = escapeReportHtml(row.activity_count || '');
                    var dt = escapeReportHtml(row.date || '');
                    var nameCell = studentIdUsable(sid)
                        ? '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;"><a href="/students/' + encodeURIComponent(String(sid)) + '" class="student-link">' + nm + '</a></td>'
                        : '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;">' + nm + '</td>';
                    var canAct = studentIdUsable(sid);
                    var dis = canAct ? '' : ' disabled';
                    var dataId = canAct ? escapeReportHtml(String(sid)) : '';
                    var actions =
                        '<td class="td-actions text-end" style="padding:12px 14px;vertical-align:middle;">' +
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

            /* renderStudentTable(data, els): data may include title, rows, student_breakdown, groups; els includes title, empty, wrap, theadRow, tbody, table, optional singleTableWrap and groupedHost. */
            window.renderStudentTable = function (data, els) {
                var title = (data && data.title) ? data.title : 'Details';
                if (els.title) {
                    els.title.textContent = title;
                }

                function clearGroupedSortIds(baseId, groupCount) {
                    var k;
                    for (k = 0; k < groupCount; k++) {
                        window._reportDrilldownSortState.delete(baseId + '-g' + k);
                    }
                }

                function resetGroupedUi() {
                    if (els.groupedHost) {
                        els.groupedHost.style.display = 'none';
                        els.groupedHost.innerHTML = '';
                    }
                    if (els.singleTableWrap) {
                        els.singleTableWrap.style.display = 'block';
                    }
                }

                if (data && data.groups && Array.isArray(data.groups) && data.groups.length) {
                    if (!els.groupedHost || !els.singleTableWrap) {
                        var flat = [];
                        for (var gi = 0; gi < data.groups.length; gi++) {
                            var gr = data.groups[gi] && data.groups[gi].rows;
                            if (gr && gr.length) {
                                flat = flat.concat(gr);
                            }
                        }
                        data = { title: title, rows: flat };
                    } else {
                        var totalG = 0;
                        for (var t = 0; t < data.groups.length; t++) {
                            var rw = data.groups[t] && data.groups[t].rows;
                            totalG += (rw && rw.length) ? rw.length : 0;
                        }
                        if (totalG === 0) {
                            window._reportDrilldownSortState.delete(els.table.id);
                            clearGroupedSortIds(els.table.id, data.groups.length);
                            if (els.empty) els.empty.style.display = 'block';
                            if (els.wrap) {
                                els.wrap.style.display = 'none';
                                els.wrap.classList.remove('hh-card', 'p-3');
                            }
                            els.theadRow.innerHTML = '';
                            els.tbody.innerHTML = '';
                            resetGroupedUi();
                            return;
                        }
                        clearGroupedSortIds(els.table.id, data.groups.length);
                        if (els.empty) els.empty.style.display = 'none';
                        if (els.wrap) {
                            els.wrap.style.display = 'block';
                            els.wrap.classList.add('hh-card', 'p-3');
                        }
                        els.singleTableWrap.style.display = 'none';
                        els.groupedHost.style.display = 'block';
                        els.groupedHost.innerHTML = '';
                        window._reportDrilldownSortState.delete(els.table.id);

                        var allForLabel = [];
                        for (var u = 0; u < data.groups.length; u++) {
                            var chunk = (data.groups[u] && data.groups[u].rows) ? data.groups[u].rows : [];
                            for (var v = 0; v < chunk.length; v++) {
                                allForLabel.push(chunk[v]);
                            }
                        }
                        var metricLabelG = inferMetricColumnLabel(allForLabel.map(mapDrilldownRowToStandard));

                        for (var idx = 0; idx < data.groups.length; idx++) {
                            var group = data.groups[idx];
                            var h6 = document.createElement('h6');
                            h6.className = (group && group.heading_class) ? group.heading_class : 'mt-3 mb-2 text-warning';
                            h6.textContent = (group && group.heading) ? group.heading : '';
                            els.groupedHost.appendChild(h6);

                            var tbl = document.createElement('table');
                            var subId = els.table.id + '-g' + idx;
                            tbl.id = subId;
                            tbl.className = 'report-drilldown-table';
                            tbl.style.fontSize = '0.95rem';
                            tbl.innerHTML = '<thead><tr></tr></thead><tbody></tbody>';
                            var trh = tbl.querySelector('thead tr');
                            trh.innerHTML = buildDrilldownTheadHtml(metricLabelG);
                            var tbod = tbl.querySelector('tbody');
                            var rawG = (group && group.rows) ? group.rows : [];
                            var mappedG = rawG.map(mapDrilldownRowToStandard);
                            if (mappedG.length === 0) {
                                tbod.innerHTML = '<tr class="report-drilldown-row"><td colspan="5" class="text-white-50 small" style="padding:12px 14px;">No students in this group.</td></tr>';
                            } else {
                                renderStandardDrilldownBody(tbod, mappedG);
                                var curG = { key: null, direction: 'asc' };
                                window._reportDrilldownSortState.set(subId, {
                                    rows: mappedG,
                                    currentSort: curG,
                                    els: { tbody: tbod, table: tbl }
                                });
                            }
                            els.groupedHost.appendChild(tbl);
                        }
                        return;
                    }
                }

                resetGroupedUi();

                var rawRows = (data && data.rows) ? data.rows : ((data && data.student_breakdown) ? data.student_breakdown : []);
                if (!rawRows.length) {
                    window._reportDrilldownSortState.delete(els.table.id);
                    if (els.empty) els.empty.style.display = 'block';
                    if (els.wrap) {
                        els.wrap.style.display = 'none';
                        els.wrap.classList.remove('hh-card', 'p-3');
                    }
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
                var metricLabel = inferMetricColumnLabel(mapped);
                var currentSort = { key: null, direction: 'asc' };
                window._reportDrilldownSortState.set(els.table.id, { rows: mapped, currentSort: currentSort, els: els });

                els.theadRow.innerHTML = buildDrilldownTheadHtml(metricLabel);

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
                var addBtn = e.target.closest('button.btn-add');
                if (addBtn && !addBtn.disabled) {
                    var id = addBtn.getAttribute('data-id');
                    if (id) {
                        reportSendPoint(id, 1);
                    }
                    return;
                }
                var subBtn = e.target.closest('button.btn-sub');
                if (subBtn && !subBtn.disabled) {
                    var id2 = subBtn.getAttribute('data-id');
                    if (id2) {
                        reportSendPoint(id2, -1);
                    }
                    return;
                }
                var awardBtn = e.target.closest('button.btn-award');
                if (awardBtn && !awardBtn.disabled) {
                    var id3 = awardBtn.getAttribute('data-id');
                    if (id3 && typeof window.openAwardModal === 'function') {
                        window.openAwardModal(id3);
                    } else if (id3) {
                        window.dispatchEvent(new CustomEvent('award:open', { detail: { student_id: id3 } }));
                    }
                    return;
                }
                var commendBtn = e.target.closest('button.btn-commend');
                if (commendBtn && !commendBtn.disabled) {
                    var id4 = commendBtn.getAttribute('data-id');
                    if (id4 && typeof window.openCommendationModal === 'function') {
                        window.openCommendationModal(id4);
                    } else if (id4) {
                        window.dispatchEvent(new CustomEvent('commendation:open', { detail: { student_id: id4 } }));
                    }
                }
            });

            window.reportSendPoint = reportSendPoint;
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
