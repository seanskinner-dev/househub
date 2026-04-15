@extends('layouts.app')

@section('content')
    <style>
        .points-index-page .house-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            transition: all 0.2s ease;
        }

        .points-index-page .house-btn:hover {
            transform: scale(1.03);
        }

        .points-index-page .house-btn.gryffindor { border: 2px solid #740001; }
        .points-index-page .house-btn.slytherin { border: 2px solid #1a472a; }
        .points-index-page .house-btn.ravenclaw { border: 2px solid #3b82f6; }
        .points-index-page .house-btn.hufflepuff { border: 2px solid #ffcc00; color: #000; }

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
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.08);
        }
    </style>

    <div class="container-fluid points-index-page" style="max-width: 1200px;">

        <h1 class="h4 mb-3" style="color: #f1f5f9;">Award points</h1>

        {{-- HOUSE BUTTONS --}}
        <div class="row g-2 mb-3">
            @foreach ($houses as $house)
                @php
                    $slug = strtolower(str_replace(' ', '', $house->name ?? ''));
                    $emoji = match ($house->name ?? '') {
                        'Gryffindor' => '🦁',
                        'Slytherin' => '🐍',
                        'Ravenclaw' => '🦅',
                        'Hufflepuff' => '🦡',
                        default => '🏫',
                    };
                @endphp
                <div class="col-6 col-md-3">
                    <button type="button"
                            class="house-btn {{ $slug }}"
                            onclick="awardHouse(@json($house->name))">{{ $emoji }} +1</button>
                </div>
            @endforeach
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
                            <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
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

        function awardHouse(houseName) {
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
    </script>
@endsection
