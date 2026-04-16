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
            transition: all 0.2s ease;
            color: #f1f5f9;
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

        .points-index-page .house-bar {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
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
        }

        .points-index-page .house-item span {
            opacity: 0.7;
        }

        .student-link {
            color: #93c5fd;
            font-weight: 700;
            text-decoration: none;
        }

        .student-link:hover {
            text-decoration: underline;
        }
    </style>

    <div class="container-fluid points-index-page" style="max-width: 1200px;">

        <h1 class="h4 mb-3" style="color: #f1f5f9;">Award points</h1>

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

        {{-- SEARCH --}}
        <div class="mb-3">
            <input type="text"
                   id="student-search"
                   class="form-control"
                   style="background: #1e293b; border-color: #334155; color: #f1f5f9;"
                   placeholder="Search students..."
                   autocomplete="off">
        </div>

        <div class="row g-3">

            <div class="col-12">
                <div class="house-bar">
                    <div class="house-item gryffindor">
                        Gryffindor <span id="gryffindor-points">{{ $pillPoints['gryffindor'] }}</span>
                    </div>
                    <div class="house-item slytherin">
                        Slytherin <span id="slytherin-points">{{ $pillPoints['slytherin'] }}</span>
                    </div>
                    <div class="house-item ravenclaw">
                        Ravenclaw <span id="ravenclaw-points">{{ $pillPoints['ravenclaw'] }}</span>
                    </div>
                    <div class="house-item hufflepuff">
                        Hufflepuff <span id="hufflepuff-points">{{ $pillPoints['hufflepuff'] }}</span>
                    </div>
                </div>
            </div>

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
                        <div class="card-body small" style="max-height: 500px; overflow-y: auto;" id="recent-activity">
                            @forelse ($recent as $r)
                                @php
                                    $who = trim(($r->first_name ?? '') . ' ' . ($r->last_name ?? ''));
                                    if ($who === '') {
                                        $who = $r->house_name ?? 'House';
                                    }
                                @endphp
                                <div class="mb-3 pb-2 border-bottom border-secondary" style="border-color: #334155 !important;">
                                    <div>
                                        <strong>{{ ($r->amount > 0 ? '+' : '') . $r->amount }}</strong>
                                        {{ $who }}
                                    </div>
                                    @if (!empty($r->category))
                                        <div class="text-muted" style="color: #94a3b8 !important;">{{ $r->category }}</div>
                                    @endif
                                    @if (!empty($r->teacher))
                                        <div class="text-muted" style="color: #94a3b8 !important;">{{ $r->teacher }}</div>
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

    <script>
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

        function houseSlugToApiName(slug) {
            var map = {
                gryffindor: 'Gryffindor',
                slytherin: 'Slytherin',
                ravenclaw: 'Ravenclaw',
                hufflepuff: 'Hufflepuff'
            };
            return map[String(slug || '').toLowerCase()] || slug;
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

        function awardHouse(house) {
            var houseName = houseSlugToApiName(house);

            fetch(@json(url('/points')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    house_name: houseName,
                    amount: 1
                })
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('bad');
                    }
                    return res.json();
                })
                .then(function (data) {
                    if (data.house) {
                        bumpHouseStandingsPill(data.house, data.amount || 1);
                    }
                    if (data.recent_entry && typeof window.houseHubPrependRecentActivity === 'function') {
                        window.houseHubPrependRecentActivity(data.recent_entry);
                    }
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('House points updated');
                    }
                })
                .catch(function () {
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('Unable to update house');
                    }
                });
        }

        if (typeof window.openCommendationModal !== 'function') {
            window.openCommendationModal = function (studentId) {
                alert('Open commendation modal for student ' + studentId);
            };
        }

        if (typeof window.openAwardModal !== 'function') {
            window.openAwardModal = function (studentId) {
                alert('Open award modal for student ' + studentId);
            };
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
    </script>
@endsection
