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
            <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #334155;">
                <h3 id="house-modal-title" style="margin:0;font-size:1.1rem;">Details</h3>
                <button id="house-modal-close" type="button" style="background:transparent;border:none;color:#fff;font-size:1.4rem;cursor:pointer;" aria-label="Close">&times;</button>
            </div>
            <div style="padding:16px 18px;">
                <p id="house-empty" style="margin:0;opacity:0.9;display:none;">No rows.</p>
                <div id="house-wrap" style="display:none;overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
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

            function renderDrillDownModal(data) {
                const rows = data.rows || [];
                document.getElementById('house-modal-title').textContent = data.title || 'Details';
                const empty = document.getElementById('house-empty');
                const wrap = document.getElementById('house-wrap');
                const thead = document.getElementById('house-thead');
                const tbody = document.getElementById('house-tbody');
                if (!rows.length) {
                    empty.style.display = 'block';
                    wrap.style.display = 'none';
                    thead.innerHTML = '';
                    tbody.innerHTML = '';
                } else {
                    empty.style.display = 'none';
                    wrap.style.display = 'block';
                    const keys = Object.keys(rows[0]);
                    thead.innerHTML = keys.map(k => '<th style="text-align:left;padding:8px 10px;border-bottom:2px solid #334155;">' + k + '</th>').join('');
                    tbody.innerHTML = rows.map(function (r) {
                        return '<tr style="border-bottom:1px solid #334155;">' + keys.map(function (k) {
                            const v = r[k];
                            return '<td style="padding:8px 10px;">' + (v == null ? '' : String(v)) + '</td>';
                        }).join('') + '</tr>';
                    }).join('');
                }
                document.getElementById('house-modal-backdrop').style.display = 'flex';
            }

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

