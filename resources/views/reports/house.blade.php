@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">House Performance Report</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Term Performance Comparison</h5>
                    <p class="text-muted small mb-3">
                        Compares house point totals between this term and the prior term. Use this to identify improving and declining house performance.
                    </p>
                    <div id="house-comparison" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">House Momentum</h5>
                    <p class="text-muted small mb-3">
                        Shows relative momentum patterns for each house over the selected period. Lower trajectories highlight where engagement is slowing.
                    </p>
                    <div id="house-momentum" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Contribution Spread</h5>
                    <p class="text-muted small mb-3">
                        Indicates how widely contributions are spread within each house. Narrow spread can signal uneven participation.
                    </p>
                    <div id="house-contribution" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card hh-card mb-4 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-2">Underperformance Index</h5>
                    <p class="text-muted small mb-3">
                        Highlights houses with higher concentrations of low engagement activity. Click bars to drill into support-target students.
                    </p>
                    <div id="house-risk" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="house-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:1000;align-items:center;justify-content:center;padding:20px;">
        <div style="background:#1e293b;color:#f1f5f9;max-width:920px;width:100%;max-height:86vh;overflow:auto;border-radius:10px;box-shadow:0 20px 50px rgba(0,0,0,0.5);">
            <div class="modal-header" style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;">
                <h3 id="house-modal-title" style="margin:0;font-size:1.1rem;">Details</h3>
                <button id="house-modal-close" type="button" style="background:transparent;border:none;color:#fff;font-size:1.4rem;cursor:pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="house-modal-body" style="padding:16px 18px;">
                <p id="house-empty" style="margin:0;opacity:0.9;display:none;">No rows.</p>
                <div id="house-wrap" style="display:none;overflow-x:auto;">
                    <table id="house-drilldown-table" class="report-drilldown-table" style="font-size:0.95rem;">
                        <thead><tr id="house-thead"></tr></thead>
                        <tbody id="house-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            const data = @json($housePerformance);
            const dataUrl = @json(route('reports.data'));
            console.log('Chart data:', data);

            const names = data.map(h => h.house);
            const thisTerm = data.map(h => Number(h.this_term ?? h.term_total ?? 0));
            const previousTerm = data.map(h => Number(h.previous_term ?? h.last_term_total ?? 0));

            let houseTableData = [];
            let houseCurrentSort = { key: null, direction: 'asc' };

            function friendlyLabel(key) {
                const map = { first_name: 'First Name', last_name: 'Last Name', year_level: 'Year Level', activity_count: 'Activity', name: 'Name' };
                return map[key] || key.replace(/_/g, ' ').replace(/\b\w/g, s => s.toUpperCase());
            }

            function houseSortRows(data, key) {
                if (houseCurrentSort.key === key) {
                    houseCurrentSort.direction = houseCurrentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    houseCurrentSort.key = key;
                    houseCurrentSort.direction = 'asc';
                }
                return [...data].sort((a, b) => {
                    let valA = a[key];
                    let valB = b[key];
                    if (valA == null) valA = '';
                    if (valB == null) valB = '';
                    if (typeof valA === 'string') valA = valA.toLowerCase();
                    if (typeof valB === 'string') valB = valB.toLowerCase();
                    const na = Number(valA);
                    const nb = Number(valB);
                    if (!isNaN(na) && !isNaN(nb) && String(valA).trim() !== '' && String(valB).trim() !== '') {
                        return houseCurrentSort.direction === 'asc' ? na - nb : nb - na;
                    }
                    if (valA < valB) return houseCurrentSort.direction === 'asc' ? -1 : 1;
                    if (valA > valB) return houseCurrentSort.direction === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            function houseRenderBody(rows, keys) {
                const tbody = document.getElementById('house-tbody');
                tbody.innerHTML = rows.map((r) => {
                    return '<tr class="report-drilldown-row">' + keys.map((k) => {
                        const v = r[k];
                        if (k === 'name' && r._studentId != null) {
                            return '<td class="td-name" style="text-align:left;padding:12px 14px;vertical-align:middle;"><a href="/students/' + encodeURIComponent(String(r._studentId)) + '" class="student-link">' + (v == null ? '' : String(v)) + '</a></td>';
                        }
                        return '<td style="padding:12px 14px;vertical-align:middle;">' + (v == null ? '' : String(v)) + '</td>';
                    }).join('') + '</tr>';
                }).join('');
            }

            function renderDrillDownModal(data) {
                const rows = data.rows || [];
                document.getElementById('house-modal-title').textContent = data.title || 'Details';
                const empty = document.getElementById('house-empty');
                const wrap = document.getElementById('house-wrap');
                const thead = document.getElementById('house-thead');
                if (!rows.length) {
                    houseTableData = [];
                    empty.style.display = 'block';
                    wrap.style.display = 'none';
                    thead.innerHTML = '';
                    document.getElementById('house-tbody').innerHTML = '';
                } else {
                    empty.style.display = 'none';
                    wrap.style.display = 'block';
                    houseCurrentSort = { key: null, direction: 'asc' };
                    const normalized = rows.map(function (r) {
                        const c = { ...r };
                        if (c.id != null) {
                            c._studentId = c.id;
                            delete c.id;
                        }
                        if (Object.prototype.hasOwnProperty.call(c, 'first_name') && Object.prototype.hasOwnProperty.call(c, 'last_name')) {
                            c.name = ((c.first_name || '') + ' ' + (c.last_name || '')).trim() || '—';
                            delete c.first_name;
                            delete c.last_name;
                        }
                        return c;
                    });
                    houseTableData = normalized;
                    const keys = Object.keys(normalized[0]).filter((k) => !k.startsWith('_'));
                    thead.innerHTML = keys.map(k => '<th data-sort-key="' + k + '" style="text-align:left;padding:10px 14px;border-bottom:2px solid #334155;cursor:pointer;">' + friendlyLabel(k) + '</th>').join('');
                    houseRenderBody(houseTableData, keys);
                }
                document.getElementById('house-modal-backdrop').style.display = 'flex';
            }

            document.getElementById('house-modal-body').addEventListener('click', function (e) {
                const th = e.target.closest('th[data-sort-key]');
                if (!th || !document.getElementById('house-drilldown-table').contains(th)) return;
                const key = th.getAttribute('data-sort-key');
                if (!key || !houseTableData.length) return;
                const sorted = houseSortRows(houseTableData, key);
                houseTableData = sorted;
                const keys = Object.keys(sorted[0]).filter((k) => !k.startsWith('_'));
                houseRenderBody(sorted, keys);
            });

            function drillDown(payload) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch('/reports/drilldown', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload || {})
                })
                    .then(function (res) { return res.json(); })
                    .then(renderDrillDownModal)
                    .catch(function () {});
            }

            //-----------------------------------
            // CLEAR OLD INSTANCES
            //-----------------------------------
            document.querySelectorAll('#house-comparison, #house-momentum, #house-contribution, #house-risk')
                .forEach(el => el.innerHTML = '');

            try {
                //-----------------------------------
                // 1. TERM COMPARISON
                //-----------------------------------
                new ApexCharts(document.querySelector("#house-comparison"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                const term = config.seriesIndex === 1 ? 'previous_term' : 'this_term';
                                drillDown({ type: 'term_comparison', value: { house, term } });
                            }
                        }
                    }
                },
                series: [
                    { name: 'This Term', data: thisTerm },
                    { name: 'Previous Term', data: previousTerm }
                ],
                xaxis: { categories: names },
                title: { text: 'Term Comparison' }
                }).render();

                //-----------------------------------
                // 2. MOMENTUM
                //-----------------------------------
                new ApexCharts(document.querySelector("#house-momentum"), {
                chart: {
                    type: 'line',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'house_low', value: house });
                            }
                        }
                    }
                },
                series: [{ name: 'Momentum', data: thisTerm }],
                xaxis: { categories: names },
                title: { text: 'Momentum' }
                }).render();

                //-----------------------------------
                // 3. CONTRIBUTION
                //-----------------------------------
                const contribution = thisTerm.map(v => Math.max(1, Math.floor(v / 10)));

                new ApexCharts(document.querySelector("#house-contribution"), {
                chart: {
                    type: 'radar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = names[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'contribution_spread', value: house });
                            }
                        }
                    }
                },
                series: [{
                    name: 'Contribution',
                    data: contribution
                }],
                labels: names,
                title: { text: 'Contribution Spread' }
                }).render();

                //-----------------------------------
                // 4. RISK
                //-----------------------------------
                fetch(dataUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (apiData) {
                        const categories = apiData?.underperformance_index?.categories || names;
                        const risk = apiData?.underperformance_index?.series?.[0]?.data || [];

                        new ApexCharts(document.querySelector("#house-risk"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = categories[config.dataPointIndex];
                            if (house) {
                                drillDown({ type: 'underperformance_house', value: house });
                            }
                        }
                    }
                },
                series: [{ name: 'Underperformance Index', data: risk }],
                xaxis: { categories: categories },
                title: { text: 'Underperformance' }
                }).render();
                    })
                    .catch(function () {
                        document.querySelector("#house-risk").innerHTML = '<div class="text-white-50 small">No data available</div>';
                    });
            } catch (e) {
                console.error('Chart render failed:', e);
            }

            document.getElementById('house-modal-close').addEventListener('click', function () {
                document.getElementById('house-modal-backdrop').style.display = 'none';
            });
            document.getElementById('house-modal-backdrop').addEventListener('click', function (e) {
                if (e.target.id === 'house-modal-backdrop') {
                    document.getElementById('house-modal-backdrop').style.display = 'none';
                }
            });
        });
    </script>
@endpush

