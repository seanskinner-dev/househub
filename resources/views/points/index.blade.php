@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #020617;
        }

        .points-index-page .house-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px;
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
            transition: all 0.2s ease;
        }

        .points-index-page .house-btn:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        .points-index-page .house-btn.gryffindor { background: #740001; }
        .points-index-page .house-btn.slytherin { background: #1a472a; }
        .points-index-page .house-btn.ravenclaw { background: #3b82f6; }
        .house-btn.hufflepuff,
        .house-btn.hufflepuff span,
        .house-btn.hufflepuff strong,
        .house-btn.hufflepuff div {
            color: #1f2937 !important;
        }

        .house-btn.hufflepuff {
            background: #ffcc00 !important;
        }

        .points-index-page .recent-activity {
            position: sticky;
            top: 20px;
        }

        .points-index-page .student-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 16px;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
            color: #f1f5f9;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .points-index-page .student-card[data-house="gryffindor"] { border-left-color: #740001; }
        .points-index-page .student-card[data-house="slytherin"] { border-left-color: #1a472a; }
        .points-index-page .student-card[data-house="ravenclaw"] { border-left-color: #3b82f6; }
        .points-index-page .student-card[data-house="hufflepuff"] { border-left-color: #ffcc00; }

        .points-index-page .student-name {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .points-index-page .student-meta {
            font-size: 0.85rem;
            color: #94a3b8;
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
            border-radius: 8px;
            width: 36px;
            height: 36px;
            min-width: 36px;
        }

        .points-index-page .student-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 6px 30px rgba(0, 0, 0, 0.6),
                0 0 10px rgba(255, 255, 255, 0.08);
        }

        .points-index-page .house-standings {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .points-index-page .points-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .points-index-page .tab {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: none;
            color: #f1f5f9;
            font-weight: 700;
            cursor: pointer;
        }

        .points-index-page .tab.active {
            background: #1e293b;
            box-shadow: inset 0 0 0 2px #3b82f6;
        }

        .points-index-page .house-pill {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 12px;
            font-weight: 700;
            background: linear-gradient(145deg, #1e293b, #0f172a);
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .points-index-page .house-pill.gryffindor {
            border: 2px solid #740001;
        }

        .points-index-page .house-pill.slytherin {
            border: 2px solid #1a472a;
        }

        .points-index-page .house-pill.ravenclaw {
            border: 2px solid #3b82f6;
        }

        .points-index-page .house-pill.hufflepuff {
            border: 2px solid #ffcc00;
            color: #ffffff;

            box-shadow:
                0 0 12px rgba(255, 204, 0, 0.4),
                0 4px 20px rgba(0, 0, 0, 0.5);
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

        <div class="points-tabs">
            <button class="tab active" data-tab="points">House Points</button>
            <button class="tab" data-tab="commendations">Commendations</button>
            <button class="tab" data-tab="awards">Awards</button>
        </div>

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

        <div class="house-standings">
            <div class="house-pill gryffindor">🦁 <span id="gryffindor-points">{{ $pillPoints['gryffindor'] }}</span></div>
            <div class="house-pill slytherin">🐍 <span id="slytherin-points">{{ $pillPoints['slytherin'] }}</span></div>
            <div class="house-pill ravenclaw">🦅 <span id="ravenclaw-points">{{ $pillPoints['ravenclaw'] }}</span></div>
            <div class="house-pill hufflepuff">🦡 <span id="hufflepuff-points">{{ $pillPoints['hufflepuff'] }}</span></div>
        </div>

        {{-- HOUSE BUTTONS --}}
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3">
                <button type="button" class="house-btn gryffindor" onclick="awardHouse('gryffindor')">🦁 +1</button>
            </div>
            <div class="col-6 col-md-3">
                <button type="button" class="house-btn slytherin" onclick="awardHouse('slytherin')">🐍 +1</button>
            </div>
            <div class="col-6 col-md-3">
                <button type="button" class="house-btn ravenclaw" onclick="awardHouse('ravenclaw')">🦅 +1</button>
            </div>
            <div class="col-6 col-md-3">
                <button type="button" class="house-btn hufflepuff" onclick="awardHouse('hufflepuff')">🦡 +1</button>
            </div>
        </div>

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

            {{-- STUDENTS --}}
            <div class="col-lg-8" id="student-list">
                @foreach ($students as $student)
                    @php
                        $houseKey = strtolower($student->house_name ?? '');
                    @endphp
                    <div class="student-card mb-2"
                         data-house="{{ $houseKey }}"
                         data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
                        <div>
                            <div class="student-name">
                                <a href="/students/{{ $student->id }}" class="student-link">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </a>
                            </div>
                            <div class="student-meta">
                                Year {{ $student->year_level }}
                                |
                                {{ $student->house_name ?? '—' }}
                                |
                                {{ $student->house_points ?? 0 }} pts
                            </div>
                        </div>

                        <div class="action-group flex-shrink-0" role="group">
                            <button type="button"
                                    class="btn btn-sm btn-sub"
                                    data-id="{{ (int) $student->id }}"
                                    data-student-id="{{ (int) $student->id }}">
                                −1
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-add"
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

        var currentTab = 'points';

        function showPointsTabToast(message) {
            if (typeof window.reportShowToast === 'function') {
                window.reportShowToast(message);
            }
        }

        function pointsAwardNameByTotal(total) {
            if (total === 5) return 'Bronze';
            if (total === 10) return 'Silver';
            if (total === 15) return 'Gold';
            return null;
        }

        function checkForAutoAward(studentId, total) {
            var award = pointsAwardNameByTotal(Number(total || 0));
            if (!award) {
                return Promise.resolve(null);
            }

            return fetch(@json(url('/points/award')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    student_id: Number(studentId),
                    award_name: award + ' Award',
                    description: 'Auto-awarded after ' + String(total) + ' commendations'
                })
            }).catch(function () {
                return null;
            });
        }

        function addCommendation(studentId) {
            return fetch(@json(url('/points/commendation')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken()
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    student_id: Number(studentId)
                })
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('bad');
                    }
                    return res.json();
                })
                .then(function (data) {
                    return checkForAutoAward(studentId, data.total).then(function () {
                        location.reload();
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.tab').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.tab').forEach(function (b) {
                        b.classList.remove('active');
                    });
                    btn.classList.add('active');
                    currentTab = btn.getAttribute('data-tab') || 'points';
                    console.log('Tab changed to:', currentTab);
                });
            });
        });

        window.pointsCanPerformAction = function (requiredTab) {
            if (currentTab === requiredTab) {
                return true;
            }
            showPointsTabToast('Switch to ' + requiredTab + ' tab first');
            return false;
        };

        window.handlePointsCommendClick = function (studentId) {
            addCommendation(studentId).catch(function () {
                showPointsTabToast('Could not save commendation');
            });
            return true;
        };
    </script>
@endsection
