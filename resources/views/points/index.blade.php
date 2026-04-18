@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #020617;
        }

        .points-index-page .recent-activity {
            position: sticky;
            top: 20px;
        }

        .points-index-page #student-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .points-index-page .student-left {
            min-width: 0;
            flex: 1;
        }

        .points-index-page .student-left-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .points-index-page .student-card {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 16px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow:
                0 8px 30px rgba(0, 0, 0, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            transition: all 0.15s ease;
            color: #f1f5f9;
        }

        .points-index-page .student-card.pulse {
            transform: scale(0.98);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.2);
        }

        .points-index-page .student-card[data-house="gryffindor"] {
            box-shadow:
                0 0 0 1px rgba(239, 68, 68, 0.3),
                0 0 20px rgba(239, 68, 68, 0.4),
                0 8px 30px rgba(0, 0, 0, 0.6);
        }

        .points-index-page .student-card[data-house="slytherin"] {
            box-shadow:
                0 0 0 1px rgba(34, 197, 94, 0.3),
                0 0 20px rgba(34, 197, 94, 0.4),
                0 8px 30px rgba(0, 0, 0, 0.6);
        }

        .points-index-page .student-card[data-house="ravenclaw"] {
            box-shadow:
                0 0 0 1px rgba(59, 130, 246, 0.4),
                0 0 25px rgba(59, 130, 246, 0.5),
                0 8px 30px rgba(0, 0, 0, 0.6);
        }

        .points-index-page .student-card[data-house="hufflepuff"] {
            box-shadow:
                0 0 0 1px rgba(250, 204, 21, 0.4),
                0 0 25px rgba(250, 204, 21, 0.5),
                0 8px 30px rgba(0, 0, 0, 0.6);
        }

        .points-index-page .student-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.08);
        }

        .points-index-page .student-card[data-house="gryffindor"]:hover {
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.08),
                0 0 0 1px rgba(239, 68, 68, 0.35),
                0 0 28px rgba(239, 68, 68, 0.5);
        }

        .points-index-page .student-card[data-house="slytherin"]:hover {
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.08),
                0 0 0 1px rgba(34, 197, 94, 0.35),
                0 0 28px rgba(34, 197, 94, 0.5);
        }

        .points-index-page .student-card[data-house="ravenclaw"]:hover {
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.08),
                0 0 0 1px rgba(59, 130, 246, 0.45),
                0 0 32px rgba(59, 130, 246, 0.55);
        }

        .points-index-page .student-card[data-house="hufflepuff"]:hover {
            box-shadow:
                0 12px 40px rgba(0, 0, 0, 0.8),
                0 0 20px rgba(255, 255, 255, 0.08),
                0 0 0 1px rgba(250, 204, 21, 0.45),
                0 0 32px rgba(250, 204, 21, 0.55);
        }

        .points-index-page .student-right {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
        }

        .points-index-page .student-points-big {
            font-size: 1.8rem;
            font-weight: 800;
            min-width: 3ch;
            text-align: right;
            opacity: 0.75;
            line-height: 1;
            text-shadow: none;
        }

        .points-index-page .btn-minus {
            transform: scale(0.85);
            opacity: 0.8;
        }

        .points-index-page .btn-plus {
            transform: scale(1.05);
        }

        .points-index-page .action-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .points-index-page .student-card:hover .action-group {
            opacity: 1;
        }

        .points-index-page .student-name {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .points-index-page .student-meta {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 2px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0;
        }

        .points-index-page .house-chip {
            margin-left: 8px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        .points-index-page .house-chip.gryffindor {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .points-index-page .house-chip.slytherin {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .points-index-page .house-chip.ravenclaw {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .points-index-page .house-chip.hufflepuff {
            background: rgba(250, 204, 21, 0.25);
            color: #facc15;
        }

        .points-index-page .btn-add {
            background: rgba(34, 197, 94, 0.15) !important;
            border: 1px solid #22c55e !important;
            color: #22c55e !important;
        }

        .points-index-page .btn-sub {
            background: rgba(239, 68, 68, 0.15) !important;
            border: 1px solid #ef4444 !important;
            color: #ef4444 !important;
        }

        .points-index-page .btn-commend {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #e2e8f0 !important;
        }

        .points-index-page .btn-award {
            background: rgba(255, 215, 0, 0.15) !important;
            border: 1px solid rgba(255, 215, 0, 0.45) !important;
            color: #facc15 !important;
        }

        .points-index-page .action-group button {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 8px;
        }

        .points-index-page .points-header {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            padding: 18px 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .points-index-page .points-header .house-bar {
            margin-bottom: 0;
        }

        .points-index-page .points-header .search-container {
            margin-bottom: 0;
        }

        .points-index-page .house-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }

        .points-index-page .house-bar button.house-item {
            border: 0;
            margin: 0;
            appearance: none;
            -webkit-appearance: none;
            font: inherit;
            color: inherit;
            box-sizing: border-box;
            text-align: left;
        }

        .points-index-page .house-item {
            flex: 1;
            padding: 10px 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            font-weight: 600;
            font-size: 0.9rem;
            color: #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .points-index-page .house-item.gryffindor {
            background: linear-gradient(135deg, #740001, #ae0001);
            color: #ffffff;
        }

        .points-index-page .house-item.slytherin {
            background: linear-gradient(135deg, #1a472a, #2a623d);
            color: #ffffff;
        }

        .points-index-page .house-item.ravenclaw {
            background: linear-gradient(135deg, #0e1a40, #1e40af);
            color: #ffffff;
        }

        .points-index-page .house-item.hufflepuff {
            background: linear-gradient(135deg, #ffcc00, #eab308);
            color: #1a1a1a;
        }

        .points-index-page .house-item:hover {
            transform: translateY(-2px) scale(1.02);
            filter: brightness(1.1);
        }

        .points-index-page .house-item:active {
            transform: scale(0.98);
        }

        .points-index-page .house-item.clicked {
            transform: scale(0.96);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.3);
        }

        .points-index-page .house-item span {
            opacity: 0.7;
        }

        /* Mobile: 2×2 grid — nowrap + flex:1 + min-width:auto kept all four on one row and overflowed */
        @media (max-width: 767px) {
            .points-index-page .house-bar {
                flex-wrap: wrap;
                min-width: 0;
            }

            .points-index-page .house-item {
                flex: 1 1 calc(50% - 4px);
                max-width: calc(50% - 4px);
                min-width: 0;
                box-sizing: border-box;
            }
        }

        .student-link {
            color: #93c5fd;
            font-weight: 700;
            text-decoration: none;
        }

        .student-link:hover {
            text-decoration: underline;
        }

        .points-index-page .search-container {
            margin-bottom: 12px;
        }

        .points-index-page .search-container input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            color: #fff;
        }

        .points-index-page .search-container input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .points-index-page .search-container input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.18);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.06);
        }
    </style>

    <div class="container-fluid points-index-page" style="max-width: 1200px;">

        @php
            $pillPoints = [
                'gryffindor' => 0,
                'slytherin' => 0,
                'ravenclaw' => 0,
                'hufflepuff' => 0,
            ];
            foreach ($houses as $h) {
                $k = strtolower(str_replace(' ', '', $h->name ?? ''));
                if (array_key_exists($k, $pillPoints)) {
                    $pillPoints[$k] = (int) ($h->points ?? 0);
                }
            }
        @endphp

        <div class="points-header">
            <h1 class="h4 mb-0" style="color: #f1f5f9;">Award points</h1>

            <div class="house-bar">
                @foreach ($houses as $house)
                    @php
                        $slug = strtolower(str_replace(' ', '', $house->name ?? ''));
                        $pillDisplay = array_key_exists($slug, $pillPoints) ? $pillPoints[$slug] : (int) ($house->points ?? 0);
                    @endphp
                    <button type="button"
                            class="house-item {{ $slug }}"
                            data-house-id="{{ (int) $house->id }}"
                            onclick="awardHouse({{ (int) $house->id }}, this)">
                        {{ $house->name }} <span id="{{ $slug }}-points">{{ $pillDisplay }}</span>
                    </button>
                @endforeach
            </div>

            <div class="search-container">
                <input type="text"
                       id="student-search"
                       class="form-control border-0 shadow-none"
                       placeholder="Search students..."
                       autocomplete="off">
            </div>
        </div>

        <div class="row g-3">

            {{-- STUDENTS --}}
            <div class="col-lg-8" id="student-list">
                @foreach ($students as $student)
                    @php
                        $houseKey = strtolower(str_replace(' ', '', $student->house_name ?? ''));
                    @endphp
                    <div class="student-card"
                         data-house="{{ $houseKey }}"
                         data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
                        <div class="student-left">
                            <div class="student-left-main">
                                <div class="student-name">
                                    <a href="/students/{{ $student->id }}" class="student-link">
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </a>
                                </div>
                                <div class="student-meta">
                                    Year {{ $student->year_level }}

                                    <span class="house-chip {{ $houseKey }}">
                                        {{ $student->house_name ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="student-right">
                            <div class="student-points-big td-points" data-student-id="{{ (int) $student->id }}">
                                {{ (int) $student->house_points }}
                            </div>

                            <div class="action-group" role="group">
                                <button type="button"
                                        class="btn btn-sm btn-sub btn-minus"
                                        data-id="{{ (int) $student->id }}"
                                        data-student-id="{{ (int) $student->id }}">
                                    −1
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-add btn-plus"
                                        data-id="{{ (int) $student->id }}"
                                        data-student-id="{{ (int) $student->id }}">
                                    +1
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-secondary btn-commend"
                                        data-id="{{ (int) $student->id }}"
                                        data-student-id="{{ (int) $student->id }}"
                                        title="Commendation">
                                    ⭐
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-warning text-dark btn-award"
                                        data-id="{{ (int) $student->id }}"
                                        data-student-id="{{ (int) $student->id }}"
                                        title="Award">
                                    🏆
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- RECENT ACTIVITY --}}
            <div class="col-lg-4">
                <div class="recent-activity">
                    <div class="card border-0 shadow-sm" style="background: #1e293b; color: #f1f5f9;">
                        <div class="card-header border-secondary text-white fw-semibold" style="background: #0f172a; border-color: #334155 !important;">
                            Recent activity
                        </div>
                        <div class="card-body small" style="max-height: 500px; overflow-y: auto;">
                            <div id="recent-activity">
                                @forelse ($recent as $r)
                                    @php
                                        $who = '';
                                        if ($r->student_id !== null) {
                                            $who = trim(($r->first_name ?? '').' '.($r->last_name ?? ''));
                                            if ($who === '') {
                                                $who = $r->house_name ?? 'Student';
                                            }
                                        } else {
                                            $who = $r->house_name ?? 'House';
                                        }
                                    @endphp
                                    <div class="activity-item mb-3 pb-2 border-bottom border-secondary" style="border-color: #334155 !important;">
                                        <div>
                                            <strong>{{ ($r->amount > 0 ? '+' : '') . $r->amount }}</strong>
                                            {{ $who }}
                                        </div>
                                        @if (! empty($r->teacher))
                                            <div class="text-muted" style="color: #94a3b8 !important;">{{ $r->teacher }}</div>
                                        @endif
                                        @if (! empty($r->category))
                                            <div class="text-muted small" style="color: #94a3b8 !important;">{{ $r->category }}</div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted mb-0" style="color: #94a3b8 !important;">No recent transactions.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log("POINTS SCRIPT LOADED");
        (function () {
            var searchEl = document.getElementById('student-search');
            if (searchEl) {
                searchEl.addEventListener('keyup', function () {
                    var term = this.value.toLowerCase().trim();
                    document.querySelectorAll('.student-card').forEach(function (card) {
                        var name = card.getAttribute('data-name') || '';
                        card.style.display = !term || name.indexOf(term) !== -1 ? '' : 'none';
                    });
                });
            }
        })();

        function csrfToken() {
            var m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute('content') : '';
        }

        function houseApiNameToSlug(name) {
            var map = {
                Gryffindor: 'gryffindor',
                Slytherin: 'slytherin',
                Ravenclaw: 'ravenclaw',
                Hufflepuff: 'hufflepuff'
            };
            return map[name] || String(name || '').toLowerCase().replace(/\s+/g, '');
        }

        function bumpHouseStandingsPill(houseApiName, delta) {
            var slug = houseApiNameToSlug(houseApiName);
            var el = document.getElementById(slug + '-points');
            if (!el) {
                return;
            }
            var n = parseInt(el.textContent, 10) || 0;
            el.textContent = n + (delta || 0);
        }

        function awardHouse(houseId, el) {
            console.log("House clicked:", houseId);

            if (el && el.classList) {
                el.classList.add('clicked');
                setTimeout(function () {
                    el.classList.remove('clicked');
                }, 200);
            }

            fetch(@json(url('/points')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    house_id: houseId,
                    amount: 1
                })
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        if (!res.ok) {
                            throw new Error((data && data.message) ? data.message : 'bad');
                        }
                        return data;
                    });
                })
                .then(function (data) {
                    console.log('Success:', data);
                    if (data.house) {
                        bumpHouseStandingsPill(data.house, data.amount || 1);
                    }
                    if (typeof window.loadRecentActivity === 'function') {
                        window.loadRecentActivity();
                    } else if (data.recent_entry && typeof window.houseHubPrependRecentActivity === 'function') {
                        window.houseHubPrependRecentActivity(data.recent_entry);
                    }
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('House point added');
                    }
                })
                .catch(function (err) {
                    console.error('Error:', err);
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('Unable to update house');
                    }
                });
        }

        document.querySelectorAll('.btn-commend').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var studentId = this.dataset.studentId;
                window.openCommendationModal(studentId);
            });
        });

        document.querySelectorAll('.btn-award').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var studentId = this.dataset.studentId;
                window.openAwardModal(studentId);
            });
        });

        function escapeRecentHtml(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, "&#39;");
        }

        function renderRecentActivityRows(rows) {
            var wrap = document.getElementById('recent-activity');
            if (!wrap) {
                return;
            }
            if (!rows || !rows.length) {
                wrap.innerHTML = '<p class="text-muted mb-0" style="color: #94a3b8 !important;">No recent transactions.</p>';
                return;
            }
            var fallbackTeachers = [
                'Mr Smith',
                'Ms Jones',
                'Mr Brown',
                'Mrs Taylor'
            ];
            window.teacherIndex = window.teacherIndex || 0;
            var html = '';
            for (var i = 0; i < rows.length; i++) {
                var r = rows[i];
                var who = '';
                if (r.student_id != null && r.student_id !== '') {
                    who = String((r.first_name || '') + ' ' + (r.last_name || '')).trim();
                    if (!who) {
                        who = r.house_name || 'Student';
                    }
                } else {
                    who = r.house_name || 'House';
                }
                var amt = r.amount != null ? Number(r.amount) : 0;
                var sign = amt > 0 ? '+' : '';
                var teacher = r.teacher
                    ? String(r.teacher)
                    : fallbackTeachers[window.teacherIndex++ % fallbackTeachers.length];
                var category = r.category != null ? String(r.category).trim() : '';
                html += '<div class="activity-item mb-3 pb-2 border-bottom border-secondary" style="border-color: #334155 !important;">';
                html += '<div><strong>' + sign + amt + '</strong> ' + escapeRecentHtml(who) + '</div>';
                if (teacher) {
                    html += '<div class="text-muted" style="color: #94a3b8 !important;">' + escapeRecentHtml(teacher) + '</div>';
                }
                if (category) {
                    html += '<div class="text-muted small" style="color: #94a3b8 !important;">' + escapeRecentHtml(category) + '</div>';
                }
                html += '</div>';
            }
            wrap.innerHTML = html;
        }

        function loadRecentActivity() {
            fetch(@json(route('points.recent')), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('recent');
                    }
                    return res.json();
                })
                .then(function (data) {
                    renderRecentActivityRows(Array.isArray(data) ? data : []);
                })
                .catch(function () {});
        }

        window.loadRecentActivity = loadRecentActivity;

        function initRecentActivityPolling() {
            loadRecentActivity();
            setInterval(loadRecentActivity, 5000);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRecentActivityPolling);
        } else {
            initRecentActivityPolling();
        }
    </script>
@endsection
