<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HouseHub</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Hide scrollbars but keep scroll */
        html,
        body {
            scrollbar-width: none; /* Firefox */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        *::-webkit-scrollbar {
            display: none; /* Chrome, Safari */
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #0f172a;
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

        .user-indicator {
            font-size: 12px;
            opacity: 0.7;
            color: #cbd5e1;
            margin-left: 8px;
            white-space: nowrap;
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

        .apexcharts-tooltip {
            background: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
        }

        .apexcharts-tooltip-title {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .apexcharts-xaxistooltip,
        .apexcharts-yaxistooltip {
            background: #0f172a !important;
            color: #e2e8f0 !important;
            border: 1px solid #334155 !important;
        }

        .apexcharts-text,
        .apexcharts-xaxis-label,
        .apexcharts-yaxis-label {
            fill: #94a3b8 !important;
        }

        .apexcharts-canvas svg rect[fill="#fefefe"] {
            fill: transparent !important;
        }

        .apexcharts-data-label,
        .apexcharts-data-label text,
        .apexcharts-point-annotation-label text {
            fill: #f8fafc !important;
        }

        .apexcharts-datalabel-label,
        .apexcharts-datalabel-value {
            fill: #f8fafc !important;
        }

        .apexcharts-tooltip text {
            fill: #f8fafc !important;
        }

        .apexcharts-point-annotation-label,
        .apexcharts-xaxis-annotation-label,
        .apexcharts-yaxis-annotation-label {
            background: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
            font-weight: 600 !important;
        }

        .apexcharts-point-annotation-label text,
        .apexcharts-xaxis-annotation-label text,
        .apexcharts-yaxis-annotation-label text {
            fill: #f8fafc !important;
        }

        .apexcharts-data-labels rect,
        .apexcharts-point-annotation-label rect,
        .apexcharts-xaxis-annotation-label rect,
        .apexcharts-yaxis-annotation-label rect {
            fill: #1e293b !important;
            stroke: #334155 !important;
        }

        .apexcharts-point-annotation-label rect {
            opacity: 0.95;
        }

        .btn-add,
        .btn-sub {
            background-color: transparent !important;
            border: 1px solid #334155 !important;
            color: #e2e8f0 !important;
            min-width: 28px;
            height: 28px;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .btn-add {
            border-color: #22c55e !important;
            color: #22c55e !important;
        }

        .btn-sub {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        .action-group {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            align-items: center;
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
            <div class="user-indicator">
                @auth
                    {{ auth()->user()?->name ?? 'System' }}
                @else
                    Guest
                @endauth
            </div>
        </div>
    </div>

    {{-- @yield for @extends; $slot for <x-app-layout> (both may be empty; only one is used) --}}
    <div class="page-content">
        @yield('content')
        {{ $slot ?? '' }}
    </div>

    <div id="commendationModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;align-items:center;justify-content:center;padding:16px;">
        <div role="dialog" aria-modal="true" aria-labelledby="commendationModalTitle" style="background:#1e293b;color:#f1f5f9;max-width:480px;width:100%;border-radius:10px;padding:20px;border:1px solid #334155;">
            <h2 id="commendationModalTitle" class="h5 mb-3">Commendation</h2>
            <input type="hidden" id="commendationModalStudentId" value="">
            <label class="form-label small text-white-50" for="commendationModalText">Message</label>
            <textarea id="commendationModalText" class="form-control mb-3" rows="4" style="background:#0f172a;border-color:#334155;color:#f1f5f9;" placeholder="Describe the commendation…"></textarea>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm" id="commendationModalCancel">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="commendationModalSubmit">Save</button>
            </div>
        </div>
    </div>

    <div id="awardModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;align-items:center;justify-content:center;padding:16px;">
        <div role="dialog" aria-modal="true" aria-labelledby="awardModalTitle" style="background:#1e293b;color:#f1f5f9;max-width:480px;width:100%;border-radius:10px;padding:20px;border:1px solid #334155;">
            <h2 id="awardModalTitle" class="h5 mb-3">Award</h2>
            <input type="hidden" id="awardModalStudentId" value="">
            <label class="form-label small text-white-50" for="awardModalName">Award</label>
            <select id="awardModalName" class="form-select mb-2" style="background:#0f172a;border-color:#334155;color:#f1f5f9;">
                <option value="Academic Excellence">Academic Excellence</option>
                <option value="Sporting Achievement">Sporting Achievement</option>
                <option value="Leadership">Leadership</option>
                <option value="Community Service">Community Service</option>
                <option value="Creative Arts">Creative Arts</option>
                <option value="Other">Other (use description)</option>
            </select>
            <label class="form-label small text-white-50" for="awardModalDescription">Description</label>
            <textarea id="awardModalDescription" class="form-control mb-3" rows="4" style="background:#0f172a;border-color:#334155;color:#f1f5f9;" placeholder="Why are they receiving this?"></textarea>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm" id="awardModalCancel">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="awardModalSubmit">Save</button>
            </div>
        </div>
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

        /** Full dd/mm/yyyy for report drill-down tables only (charts use formatReportChartDate). */
        window.formatReportDrilldownDate = function (d) {
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
                return m[3] + '/' + m[2] + '/' + m[1];
            }
            var dt = new Date(s.length === 10 ? s + 'T00:00:00' : s);
            if (!isNaN(dt.getTime())) {
                var dd = String(dt.getDate()).padStart(2, '0');
                var mm = String(dt.getMonth() + 1).padStart(2, '0');
                var yyyy = String(dt.getFullYear());
                return dd + '/' + mm + '/' + yyyy;
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

            window.houseHubPrependRecentActivity = function (entry) {
                if (!entry) {
                    return;
                }
                var wrap = document.getElementById('recent-activity');
                if (!wrap) {
                    return;
                }
                var emptyMsg = wrap.querySelector('p.text-muted');
                if (emptyMsg && emptyMsg.textContent && emptyMsg.textContent.indexOf('No recent') !== -1) {
                    emptyMsg.remove();
                }
                var who = entry.who != null ? String(entry.who) : '—';
                var amt = entry.amount != null ? Number(entry.amount) : 0;
                var sign = amt > 0 ? '+' : '';
                var teacher = entry.teacher != null ? String(entry.teacher) : '';
                var row = document.createElement('div');
                row.className = 'activity-item mb-3 pb-2 border-bottom border-secondary';
                row.style.borderColor = '#334155';
                var html = '<div><strong>' + sign + amt + '</strong> ' + escapeReportHtml(who) + '</div>';
                if (teacher) {
                    html += '<div class="text-muted" style="color: #94a3b8 !important;">' + escapeReportHtml(teacher) + '</div>';
                }
                row.innerHTML = html;
                wrap.insertBefore(row, wrap.firstChild);
            };

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
                    dateStr = typeof window.formatReportDrilldownDate === 'function' ? window.formatReportDrilldownDate(slice) : slice;
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
                return (
                    '<th data-sort-key="name" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Name</th>' +
                    '<th data-sort-key="year_level" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Year Level</th>' +
                    '<th data-sort-key="activity_count" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Points</th>' +
                    '<th data-sort-key="date" class="report-sort-th" style="text-align:left;padding:12px 14px;border-bottom:2px solid #334155;">Date</th>' +
                    '<th class="text-end" style="padding:12px 14px;border-bottom:2px solid #334155;">Actions</th>'
                );
            }

            function reportShowToast(message) {
                var el = document.getElementById('report-global-toast');
                if (!el) {
                    el = document.createElement('div');
                    el.id = 'report-global-toast';
                    el.style.cssText = 'position:fixed;right:20px;bottom:20px;background:#0f172a;color:#e2e8f0;border:1px solid #334155;padding:10px 14px;border-radius:8px;z-index:3000;display:none;';
                    document.body.appendChild(el);
                }
                el.textContent = message;
                el.style.display = 'block';
                setTimeout(function () { el.style.display = 'none'; }, 1500);
            }

            function reportSendPoint(studentId, amount) {
                function showToast(message, type) {
                    if (type === 'error') {
                        reportShowToast(message || 'Unable to update points');
                        return;
                    }
                    reportShowToast(message || 'Points updated');
                }

                function updateUI(data) {
                    if (data && data.recent_entry) {
                        window.houseHubPrependRecentActivity(data.recent_entry);
                    }

                    var el = document.querySelector('[data-student-id="' + String(studentId) + '"].td-points');
                    if (!el) {
                        return;
                    }

                    var start = parseInt(el.innerText || '0', 10);
                    if (isNaN(start)) {
                        start = 0;
                    }
                    var end = parseInt(data && data.points != null ? data.points : start, 10);
                    if (isNaN(end)) {
                        end = start;
                    }

                    var i = start;
                    var interval = setInterval(function () {
                        if (i === end) {
                            clearInterval(interval);
                        } else {
                            i += (end > start ? 1 : -1);
                            el.innerText = String(i);
                        }
                    }, 20);

                    var card = el.closest('.student-card');
                    if (card) {
                        card.classList.add('pulse');
                        setTimeout(function () {
                            card.classList.remove('pulse');
                        }, 150);
                    }
                }

                fetch('/points', {
                    credentials: 'same-origin',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        amount: amount
                    })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data && data.success) {
                            updateUI(data);
                            showToast('Points updated');
                        } else {
                            showToast('Failed to update', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Unable to update points', 'error');
                    });
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
                    var pointsValue = row.points != null ? row.points : row.activity_count;
                    var pointsInt = parseInt(pointsValue, 10);
                    if (isNaN(pointsInt)) {
                        pointsInt = 0;
                    }
                    var pts = escapeReportHtml(String(pointsInt));
                    var dt = escapeReportHtml(row.date || '');
                    var nameCell = studentIdUsable(sid)
                        ? '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;"><a href="/students/' + encodeURIComponent(String(sid)) + '" class="student-link">' + nm + '</a></td>'
                        : '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;">' + nm + '</td>';
                    var canAct = studentIdUsable(sid);
                    var dis = canAct ? '' : ' disabled';
                    var dataId = canAct ? escapeReportHtml(String(sid)) : '';
                    var dataStudentAttr = canAct ? ' data-student-id="' + dataId + '"' : '';
                    var actions =
                        '<td class="td-actions text-end" style="padding:12px 14px;vertical-align:middle;">' +
                        '<div class="action-group flex-wrap">' +
                        '<button type="button" class="btn-sub btn-minus btn btn-sm"' + dis + ' data-id="' + dataId + '"' + dataStudentAttr + '>-1</button>' +
                        '<button type="button" class="btn-add btn-plus btn btn-sm"' + dis + ' data-id="' + dataId + '"' + dataStudentAttr + '>+1</button>' +
                        '<button type="button" class="btn-award btn btn-sm"' + dis + ' data-id="' + dataId + '"' + dataStudentAttr + '>🏆</button>' +
                        '<button type="button" class="btn-commend btn-commendation btn btn-sm"' + dis + ' data-id="' + dataId + '"' + dataStudentAttr + '>⭐</button>' +
                        '</div></td>';
                    return '<tr class="report-drilldown-row">' + nameCell +
                        '<td style="text-align:left;padding:12px 14px;vertical-align:middle;">' + yl + '</td>' +
                        '<td class="td-points" data-student-id="' + escapeReportHtml(String(sid || '')) + '" style="text-align:left;padding:12px 14px;vertical-align:middle;">' + pts + '</td>' +
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
                var addBtn = e.target.closest('button.btn-add, button.btn-plus');
                if (addBtn && !addBtn.disabled) {
                    e.preventDefault();
                    var id = addBtn.getAttribute('data-student-id') || addBtn.getAttribute('data-id');
                    if (id) {
                        if (typeof window.awardPoint === 'function') {
                            window.awardPoint(id, 1);
                        } else {
                            reportSendPoint(id, 1);
                        }
                    }
                    return;
                }
                var subBtn = e.target.closest('button.btn-sub, button.btn-minus');
                if (subBtn && !subBtn.disabled) {
                    e.preventDefault();
                    var id2 = subBtn.getAttribute('data-student-id') || subBtn.getAttribute('data-id');
                    if (id2) {
                        if (typeof window.awardPoint === 'function') {
                            window.awardPoint(id2, -1);
                        } else {
                            reportSendPoint(id2, -1);
                        }
                    }
                    return;
                }
                var awardBtn = e.target.closest('button.btn-award');
                if (awardBtn && !awardBtn.disabled) {
                    e.preventDefault();
                    var id3 = awardBtn.getAttribute('data-student-id') || awardBtn.getAttribute('data-id');
                    if (id3) {
                        window.openAwardModal(id3);
                    }
                    return;
                }
                var commendBtn = e.target.closest('button.btn-commend, button.btn-commendation');
                if (commendBtn && !commendBtn.disabled) {
                    e.preventDefault();
                    var id4 = commendBtn.getAttribute('data-student-id') || commendBtn.getAttribute('data-id');
                    if (id4) {
                        window.openCommendationModal(id4);
                    }
                }
            }, false);

            document.addEventListener('click', function (e) {
                var t = e.target.closest('button.btn-add, button.btn-sub');
                if (t) {
                    e.preventDefault();
                }
            }, true);

            function houseHubCsrf() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : '';
            }

            window.openCommendationModal = function (studentId) {
                var sid = studentId != null ? String(studentId).trim() : '';
                if (!sid) {
                    return;
                }
                var inp = document.getElementById('commendationModalStudentId');
                var ta = document.getElementById('commendationModalText');
                var shell = document.getElementById('commendationModal');
                if (!inp || !ta || !shell) {
                    return;
                }
                inp.value = sid;
                ta.value = '';
                shell.style.display = 'flex';
            };

            function closeCommendationModal() {
                var shell = document.getElementById('commendationModal');
                if (shell) {
                    shell.style.display = 'none';
                    shell.classList.remove('show');
                }
                if (window.bootstrap && typeof window.bootstrap.Modal !== 'undefined') {
                    var cModal = window.bootstrap.Modal.getInstance(document.getElementById('commendationModal'));
                    if (cModal) {
                        cModal.hide();
                    }
                }
            }

            window.openAwardModal = function (studentId) {
                var sid = studentId != null ? String(studentId).trim() : '';
                if (!sid) {
                    return;
                }
                var inp = document.getElementById('awardModalStudentId');
                var shell = document.getElementById('awardModal');
                var desc = document.getElementById('awardModalDescription');
                if (!inp || !shell) {
                    return;
                }
                inp.value = sid;
                if (desc) {
                    desc.value = '';
                }
                shell.style.display = 'flex';
            };

            function closeAwardModal() {
                var shell = document.getElementById('awardModal');
                if (shell) {
                    shell.style.display = 'none';
                    shell.classList.remove('show');
                }
                if (window.bootstrap && typeof window.bootstrap.Modal !== 'undefined') {
                    var aModal = window.bootstrap.Modal.getInstance(document.getElementById('awardModal'));
                    if (aModal) {
                        aModal.hide();
                    }
                }
            }

            function closeAndResetRewardModals() {
                closeCommendationModal();
                closeAwardModal();
                document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
                var commendForm = document.querySelector('#commendationForm');
                var awardForm = document.querySelector('#awardForm');
                if (commendForm && typeof commendForm.reset === 'function') {
                    commendForm.reset();
                } else {
                    var commendText = document.getElementById('commendationModalText');
                    if (commendText) commendText.value = '';
                }
                if (awardForm && typeof awardForm.reset === 'function') {
                    awardForm.reset();
                } else {
                    var awardNameEl = document.getElementById('awardModalName');
                    var awardDescEl = document.getElementById('awardModalDescription');
                    if (awardNameEl) awardNameEl.selectedIndex = 0;
                    if (awardDescEl) awardDescEl.value = '';
                }
            }

            (function wireHouseHubModals() {
                var cShell = document.getElementById('commendationModal');
                if (cShell) {
                    cShell.addEventListener('click', function (e) {
                        if (e.target.id === 'commendationModal') {
                            closeCommendationModal();
                        }
                    });
                }
                var cCancel = document.getElementById('commendationModalCancel');
                if (cCancel) {
                    cCancel.addEventListener('click', closeCommendationModal);
                }

                var aShell = document.getElementById('awardModal');
                if (aShell) {
                    aShell.addEventListener('click', function (e) {
                        if (e.target.id === 'awardModal') {
                            closeAwardModal();
                        }
                    });
                }
                var aCancel = document.getElementById('awardModalCancel');
                if (aCancel) {
                    aCancel.addEventListener('click', closeAwardModal);
                }
            })();

            window.reportSendPoint = reportSendPoint;
            window.reportShowToast = reportShowToast;
        })();

        window.Apex = {
            chart: {
                background: 'transparent',
                toolbar: { show: false },
                foreColor: '#e2e8f0'
            },

            theme: {
                mode: 'dark'
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
                    fontSize: '12px',
                    fontWeight: 600
                },
                background: {
                    enabled: false
                }
            },

            tooltip: {
                theme: 'dark',
                style: {
                    fontSize: '13px',
                    color: '#f8fafc'
                },
                marker: {
                    show: true
                },
                x: {
                    show: true
                },
                y: {
                    formatter: function (val) {
                        return String(val) + ' points';
                    }
                }
            },

            colors: [
                '#0ea5e9',
                '#22c55e',
                '#f59e0b',
                '#ef4444',
                '#a855f7'
            ]
        };

        window.hhApplyApexDefaults = function (options) {
            options = options || {};
            const apex = window.Apex || {};

            function mergeAxisWithLabelColor(baseAxis, optAxis) {
                baseAxis = baseAxis || {};
                optAxis = optAxis || {};
                return {
                    ...baseAxis,
                    ...optAxis,
                    labels: {
                        ...(baseAxis.labels || {}),
                        ...(optAxis.labels || {}),
                        style: {
                            ...((baseAxis.labels && baseAxis.labels.style) || {}),
                            ...((optAxis.labels && optAxis.labels.style) || {}),
                            colors: '#94a3b8'
                        }
                    }
                };
            }

            let mergedYaxis;
            if (Array.isArray(options.yaxis)) {
                mergedYaxis = options.yaxis.map(function (axis) {
                    return mergeAxisWithLabelColor(apex.yaxis, axis);
                });
            } else {
                mergedYaxis = mergeAxisWithLabelColor(apex.yaxis, options.yaxis);
            }

            const merged = {
                ...apex,
                ...options,
                chart: {
                    ...apex.chart,
                    ...(options.chart || {}),
                    background: 'transparent',
                    foreColor: '#e2e8f0'
                },
                grid: {
                    ...apex.grid,
                    ...(options.grid || {}),
                    borderColor: '#334155'
                },
                xaxis: mergeAxisWithLabelColor(apex.xaxis, options.xaxis),
                yaxis: mergedYaxis,
                tooltip: {
                    ...apex.tooltip,
                    ...(options.tooltip || {}),
                    theme: 'dark',
                    style: {
                        ...((apex.tooltip && apex.tooltip.style) || {}),
                        ...(((options.tooltip || {}).style) || {}),
                        fontSize: '13px',
                        color: '#f8fafc'
                    }
                }
            };
            var chartType = (options.chart && options.chart.type) ? String(options.chart.type).toLowerCase() : '';
            merged.dataLabels = {
                ...(merged.dataLabels || {}),
                ...(options.dataLabels || {}),
                enabled: chartType === 'bar',
                style: {
                    ...((merged.dataLabels && merged.dataLabels.style) || {}),
                    ...((options.dataLabels && options.dataLabels.style) || {}),
                    colors: ['#f8fafc']
                },
                background: {
                    ...((merged.dataLabels && merged.dataLabels.background) || {}),
                    ...((options.dataLabels && options.dataLabels.background) || {}),
                    enabled: false
                }
            };

            if (options.chart && options.chart.type === 'heatmap') {
                merged.plotOptions = merged.plotOptions || {};
                merged.plotOptions.heatmap = {
                    ...(merged.plotOptions.heatmap || {}),
                    shadeIntensity: 0.6,
                    radius: 4,
                    useFillColorAsStroke: false,
                    colorScale: {
                        ranges: [
                            { from: 0, to: 50, color: '#0f172a' },
                            { from: 51, to: 100, color: '#1e293b' },
                            { from: 101, to: 150, color: '#0ea5e9' },
                            { from: 151, to: 200, color: '#22c55e' }
                        ]
                    }
                };
                merged.stroke = {
                    ...(merged.stroke || {}),
                    width: 2,
                    colors: ['#020617']
                };
            }

            if (chartType === 'line' || chartType === 'area') {
                var existingAnnotations = merged.annotations || {};
                var pointAnnotations = Array.isArray(existingAnnotations.points) ? existingAnnotations.points : [];
                merged.annotations = {
                    ...existingAnnotations,
                    yaxis: Array.isArray(existingAnnotations.yaxis) ? existingAnnotations.yaxis : [],
                    points: pointAnnotations.map(function (pt) {
                        var p = pt || {};
                        var lbl = p.label || {};
                        var lblStyle = lbl.style || {};
                        return {
                            ...p,
                            label: {
                                ...lbl,
                                style: {
                                    ...lblStyle,
                                    background: '#1e293b',
                                    color: '#f8fafc',
                                    borderColor: '#334155'
                                }
                            }
                        };
                    }),
                    xaxis: Array.isArray(existingAnnotations.xaxis) ? existingAnnotations.xaxis : []
                };
                merged.markers = {
                    ...(merged.markers || {}),
                    ...(options.markers || {}),
                    size: 4,
                    strokeWidth: 2,
                    strokeColors: '#0f172a'
                };
                merged.stroke = {
                    ...(merged.stroke || {}),
                    ...(options.stroke || {}),
                    width: 3,
                    curve: 'smooth'
                };
            }

            return merged;
        };
    </script>
    @stack('scripts')
    <script>
        (function () {
            var pointsAwardUrl = @json(route('points.award'));

            function hhCsrf() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : '';
            }

            function hhParseJsonResponse(res) {
                return res.text().then(function (text) {
                    var data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid response:', text);
                        throw new Error('Invalid response');
                    }
                    if (!res.ok) {
                        throw new Error((data && data.message) ? data.message : 'Failed');
                    }
                    return data;
                });
            }

            var saveCommendationBtn = document.getElementById('commendationModalSubmit');
            var saveAwardBtn = document.getElementById('awardModalSubmit');

            if (saveCommendationBtn) {
                saveCommendationBtn.addEventListener('click', function () {
                    var textEl = document.getElementById('commendationModalText');
                    var sidEl = document.getElementById('commendationModalStudentId');
                    var text = textEl ? textEl.value.trim() : '';
                    var sid = sidEl ? sidEl.value : '';
                    if (!sid || !text) {
                        if (typeof window.reportShowToast === 'function') {
                            window.reportShowToast('Please enter a message');
                        } else {
                            alert('Please enter a message');
                        }
                        return;
                    }
                    var selectedStudentId = Number(sid);
                    const formData = new FormData();
                    formData.append('student_id', selectedStudentId);
                    formData.append('amount', 1);
                    formData.append('category', 'commendation');
                    formData.append('description', text);

                    fetch('/points', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                        .then(async function (res) {
                            const text = await res.text();

                            console.log('STATUS:', res.status);
                            console.log('RESPONSE:', text);

                            if (!res.ok) {
                                alert('ERROR:\n' + text);
                                return;
                            }

                            location.reload();
                        })
                        .catch(function (err) {
                            console.error(err);
                            alert('Fetch completely failed');
                        });
                });
            }

            if (saveAwardBtn) {
                saveAwardBtn.addEventListener('click', function () {
                    var sidEl = document.getElementById('awardModalStudentId');
                    var nameEl = document.getElementById('awardModalName');
                    var descEl = document.getElementById('awardModalDescription');
                    var sid = sidEl ? sidEl.value : '';
                    var awardName = nameEl ? nameEl.value.trim() : '';
                    var desc = descEl ? descEl.value.trim() : '';
                    if (!sid || !awardName || !desc) {
                        if (typeof window.reportShowToast === 'function') {
                            window.reportShowToast('Please complete all fields');
                        } else {
                            alert('Please complete all fields');
                        }
                        return;
                    }
                    fetch(pointsAwardUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': hhCsrf()
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            student_id: Number(sid),
                            award_name: awardName,
                            description: desc
                        })
                    })
                        .then(hhParseJsonResponse)
                        .then(function (data) {
                            if (!data || !data.success) {
                                throw new Error('Failed');
                            }
                            location.reload();
                        })
                        .catch(function (err) {
                            console.error(err);
                            alert('Failed to save');
                        });
                });
            }
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
