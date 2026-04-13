@extends('layouts.app')

@section('content')
    <style>
        #pc-report-grid .card {
            padding: 10px;
        }

        #pc-report-grid .row {
            margin-bottom: 20px;
        }
    </style>

    <h1 style="font-size: 2rem; margin-bottom: 0.75rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.25rem; max-width: 52rem;">
        Pastoral care overview — weekday data only (Mon–Fri). Use filters and click charts for details (no page reload).
    </p>

    <div id="pc-filter-bar" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; margin-bottom: 2.5rem; padding: 18px; background: #1e293b; border-radius: 8px;">
        <div>
            <label for="pc-house" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">House</label>
            <select id="pc-house" name="house" style="min-width: 180px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                @foreach ($houses as $h)
                    <option value="{{ $h }}">{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="pc-year" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Year</label>
            <select id="pc-year" name="year" style="min-width: 140px; padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
                <option value="All">All</option>
                <option value="7">Year 7</option>
                <option value="8">Year 8</option>
                <option value="9">Year 9</option>
                <option value="10">Year 10</option>
                <option value="11">Year 11</option>
                <option value="12">Year 12</option>
            </select>
        </div>
        <div>
            <label for="pc-start" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">Start date</label>
            <input type="date" id="pc-start" name="start_date" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <label for="pc-end" style="display: block; font-size: 0.85rem; opacity: 0.85; margin-bottom: 6px;">End date</label>
            <input type="date" id="pc-end" name="end_date" style="padding: 10px 12px; font-size: 1rem; border-radius: 6px; border: 1px solid #334155; background: #0f172a; color: #fff;">
        </div>
        <div>
            <button type="button" id="pc-apply" style="padding: 11px 22px; font-size: 1rem; font-weight: 600; border: none; border-radius: 6px; background: #3b82f6; color: #fff; cursor: pointer;">
                Apply
            </button>
        </div>
    </div>

    <div id="pc-report-grid">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Engagement Health</h5>
                        <p class="small mb-3 text-white-50">
                            Shows how many students are actively receiving points. Lower segments indicate disengagement risk.
                        </p>
                        <div id="engagement-health"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Engagement Trend</h5>
                        <p class="small mb-3 text-white-50">
                            Tracks daily engagement. Sudden drops highlight days where participation is low.
                        </p>
                        <div id="engagement-trend"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Points by House</h5>
                        <p class="small mb-3 text-white-50">
                            Compares which houses are contributing most. Lower values may indicate disengaged groups.
                        </p>
                        <div id="points-by-house"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Risk Distribution</h5>
                        <p class="small mb-3 text-white-50">
                            Breaks students into engagement levels. Focus on high-risk groups to improve participation.
                        </p>
                        <div id="risk-distribution"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pc-modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
        <div id="pc-modal" role="dialog" aria-modal="true" style="background: #1e293b; color: #f1f5f9; max-width: 900px; width: 100%; max-height: 85vh; overflow: auto; border-radius: 10px; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #334155;">
                <h3 id="pc-modal-title" style="margin: 0; font-size: 1.2rem;">Details</h3>
                <button type="button" id="pc-modal-close" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; line-height: 1; cursor: pointer;" aria-label="Close">&times;</button>
            </div>
            <div id="pc-modal-body" style="padding: 16px 20px;">
                <p id="pc-drilldown-empty" style="opacity:0.9;margin:0;display:none;">No rows for this selection.</p>
                <div id="pc-drilldown-wrap" style="display:none; overflow-x: auto;">
                    <table id="pc-drilldown-table" style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                        <thead><tr id="pc-drilldown-thead-row"></tr></thead>
                        <tbody id="pc-drilldown-tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var drillUrl = @json(route('reports.drilldown'));
            var modalBackdrop = document.getElementById('pc-modal-backdrop');
            var modalClose = document.getElementById('pc-modal-close');

            function drillDown(payload) {
                var meta = document.querySelector('meta[name="csrf-token"]');
                var token = meta ? meta.getAttribute('content') : '';
                fetch(drillUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload || {})
                })
                    .then(function (res) { return res.json(); })
                    .then(renderDrillDownModal)
                    .catch(function () {});
            }

            function renderDrillDownModal(data) {
                var title = data.title || 'Details';
                var rows = data.rows || [];
                document.getElementById('pc-modal-title').textContent = title;
                var emptyEl = document.getElementById('pc-drilldown-empty');
                var wrapEl = document.getElementById('pc-drilldown-wrap');
                var theadRow = document.getElementById('pc-drilldown-thead-row');
                var tbody = document.getElementById('pc-drilldown-tbody');
                if (!rows.length) {
                    emptyEl.style.display = 'block';
                    wrapEl.style.display = 'none';
                    theadRow.innerHTML = '';
                    tbody.innerHTML = '';
                } else {
                    var keys = Object.keys(rows[0]);
                    emptyEl.style.display = 'none';
                    wrapEl.style.display = 'block';
                    theadRow.innerHTML = keys
                        .map(function (k) {
                            return '<th style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;">' + k + '</th>';
                        })
                        .join('');
                    tbody.innerHTML = rows
                        .map(function (row) {
                            return (
                                '<tr style="border-bottom:1px solid #334155;">' +
                                keys.map(function (k) {
                                    var v = row[k];
                                    return '<td style="padding:10px 12px;">' + (v == null ? '' : String(v)) + '</td>';
                                }).join('') +
                                '</tr>'
                            );
                        })
                        .join('');
                }
                modalBackdrop.style.display = 'flex';
            }

            function onAnyChartPoint(event, chartContext, config) {
                let value;

                if (config.w.config.labels) {
                    value = config.w.config.labels[config.dataPointIndex];
                }

                if (config.w.config.xaxis?.categories) {
                    value = config.w.config.xaxis.categories[config.dataPointIndex];
                }

                drillDown({
                    type: 'low',
                    value: value
                });
            }

            // TEST DATA (temporary to confirm rendering works)
            const names = ['Gryffindor', 'Slytherin', 'Ravenclaw', 'Hufflepuff'];
            const values = [120, 150, 90, 70];

            //-----------------------------------
            // ENGAGEMENT HEALTH
            //-----------------------------------
            new ApexCharts(document.querySelector("#engagement-health"), {
                chart: { type: 'donut', height: 320, events: { dataPointSelection: onAnyChartPoint } },
                series: values,
                labels: names,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                title: { text: 'Engagement Health' }
            }).render();

            //-----------------------------------
            // ENGAGEMENT TREND
            //-----------------------------------
            new ApexCharts(document.querySelector("#engagement-trend"), {
                chart: { type: 'line', height: 320, events: { dataPointSelection: onAnyChartPoint } },
                series: [{ data: values }],
                xaxis: { categories: names },
                title: { text: 'Engagement Trend' }
            }).render();

            //-----------------------------------
            // POINTS BY HOUSE
            //-----------------------------------
            new ApexCharts(document.querySelector("#points-by-house"), {
                chart: { type: 'bar', height: 320, events: { dataPointSelection: onAnyChartPoint } },
                series: [{ data: values }],
                xaxis: { categories: names },
                title: { text: 'Points by House' }
            }).render();

            //-----------------------------------
            // RISK DISTRIBUTION
            //-----------------------------------
            new ApexCharts(document.querySelector("#risk-distribution"), {
                chart: { type: 'polarArea', height: 320, events: { dataPointSelection: onAnyChartPoint } },
                series: values,
                labels: names,
                title: { text: 'Risk Distribution' }
            }).render();

            modalClose.addEventListener('click', function () {
                modalBackdrop.style.display = 'none';
            });
            modalBackdrop.addEventListener('click', function (e) {
                if (e.target.id === 'pc-modal-backdrop') {
                    modalBackdrop.style.display = 'none';
                }
            });

        });
    </script>
@endpush
