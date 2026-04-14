@extends('layouts.app')

@section('content')
    <div class="container-fluid" style="max-width: 1200px;">

        <h1 class="h4 mb-3" style="color: #f1f5f9;">Award points</h1>

        {{-- HOUSE BUTTONS --}}
        <div class="row g-2 mb-3">
            @foreach ($houses as $house)
                <div class="col-6 col-md-3">
                    <button type="button"
                            class="btn w-100 text-white fw-semibold py-2"
                            style="background-color: {{ $house->colour_hex ?? '#475569' }};"
                            onclick="awardHouse(@json($house->name))">
                        +1 {{ $house->name }}
                    </button>
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
                    <div class="card mb-2 student-card border-0 shadow-sm"
                         style="background: #1e293b; color: #f1f5f9;"
                         data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}">
                        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">

                            <div>
                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                <br>
                                <small class="text-muted" style="color: #94a3b8 !important;">
                                    Year {{ $student->year_level }}
                                    |
                                    {{ $student->house_name ?? '—' }}
                                    |
                                    {{ $student->house_points ?? 0 }} pts
                                </small>
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
