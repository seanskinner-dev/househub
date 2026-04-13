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
                        <p class="small text-white-50">
                            Shows how many students are actively receiving points. Red indicates high-risk students.
                        </p>
                        <div id="engagement-health"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Risk Distribution</h5>
                        <p class="small text-white-50">
                            Shows student risk levels. Click a segment to view affected students.
                        </p>
                        <div id="risk-distribution"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Engagement Trend</h5>
                        <p class="small text-white-50">
                            Tracks engagement over time. Click a day to see activity.
                        </p>
                        <div id="engagement-trend"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4 bg-dark text-white border-0">
                    <div class="card-body">
                        <h5>Points by House</h5>
                        <p class="small text-white-50">
                            Shows which houses are most active. Click to drill into students.
                        </p>
                        <div id="points-by-house"></div>
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
    window.pcData = @json($data ?? null);
    console.log('DATA FROM BLADE:', window.pcData);
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    console.log('SCRIPT RUNNING');
    console.log('ApexCharts:', typeof ApexCharts);
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts NOT loaded');
        return;
    }

    const drillUrl = @json(route('reports.drilldown'));
    const modalBackdrop = document.getElementById('pc-modal-backdrop');
    const modalClose = document.getElementById('pc-modal-close');

    function queryStringFromFilters() {
        const params = new URLSearchParams();
        params.set('house', document.getElementById('pc-house').value || 'All');
        params.set('year', document.getElementById('pc-year').value || 'All');
        const start = document.getElementById('pc-start').value;
        const end = document.getElementById('pc-end').value;
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        return params.toString();
    }

    function renderDrillDownModal(data) {
        const title = data.title || 'Details';
        const rows = data.rows || [];
        document.getElementById('pc-modal-title').textContent = title;
        const emptyEl = document.getElementById('pc-drilldown-empty');
        const wrapEl = document.getElementById('pc-drilldown-wrap');
        const theadRow = document.getElementById('pc-drilldown-thead-row');
        const tbody = document.getElementById('pc-drilldown-tbody');

        if (!rows.length) {
            emptyEl.style.display = 'block';
            wrapEl.style.display = 'none';
            theadRow.innerHTML = '';
            tbody.innerHTML = '';
        } else {
            const keys = Object.keys(rows[0]);
            emptyEl.style.display = 'none';
            wrapEl.style.display = 'block';
            theadRow.innerHTML = keys
                .map(k => `<th style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;">${String(k)}</th>`)
                .join('');
            tbody.innerHTML = rows
                .map(row => `<tr style="border-bottom:1px solid #334155;">${keys.map(k => `<td style="padding:10px 12px;">${String(row[k] ?? '')}</td>`).join('')}</tr>`)
                .join('');
        }
        modalBackdrop.style.display = 'flex';
    }

    function drillDown(payload) {
        if (payload && payload.type === 'risk') {
            payload = {
                type: 'risk_segment',
                value: payload.value
            };
        }
        const meta = document.querySelector('meta[name="csrf-token"]');
        const token = meta ? meta.getAttribute('content') : '';
        fetch(drillUrl + '?' + queryStringFromFilters(), {
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
            .then(res => res.json())
            .then(renderDrillDownModal)
            .catch(() => {});
    }

    const data = window.pcData;
    if (!data) {
        console.error('No data passed from backend');
        return;
    }

    console.log('PC DATA:', data);
    console.log('FULL DATA:', data);
    console.log(document.querySelector("#engagement-trend"));
    console.log(document.querySelector("#points-by-house"));

    function renderCharts(payload) {
        if (!payload || !payload.donut || !payload.trend || !payload.house_breakdown) {
            console.error('Missing required data', payload);
            return;
        }
        console.log('Risk labels:', payload.donut.labels);
        const data = payload;
        const donutSeries = data?.donut?.series || [];
        const donutLabels = data?.donut?.labels || [];
        if (donutSeries.length === 0) {
            console.error('Donut data missing or empty', data.donut);
        }
        const houseSeries = data?.house_breakdown?.series || [];
        const houseCategories = data?.house_breakdown?.categories || [];
        if (houseSeries.length === 0) {
            console.error('House breakdown missing', data.house_breakdown);
        }

        ['engagement-health', 'risk-distribution', 'engagement-trend', 'points-by-house'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = '';
        });

        if (!data.donut || !data.donut.series) {
            console.error('Donut data missing');
        }
        if (!data.donut || !data.donut.series || !data.donut.labels) {
            console.error('Donut data invalid:', data.donut);
        } else {
            try {
                const riskColors = donutLabels.map(label => {
                    const l = label.toLowerCase();
                    if (l.includes('high')) return '#ef4444';
                    if (l.includes('medium')) return '#eab308';
                    return '#22c55e';
                });

                new ApexCharts(document.querySelector("#engagement-health"), {
                    chart: {
                        type: 'donut',
                        height: 320,
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const label = config.w.config.labels[config.dataPointIndex];
                                drillDown({
                                    type: 'risk',
                                    value: label
                                });
                            }
                        }
                    },
                    series: donutSeries,
                    labels: donutLabels,
                    colors: riskColors,
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%'
                            }
                        }
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                }).render();
            } catch (e) {
                console.error('engagement-health failed', e);
            }

            try {
                const riskColors = donutLabels.map(label => {
                    const l = label.toLowerCase();
                    if (l.includes('high')) return '#ef4444';
                    if (l.includes('medium')) return '#eab308';
                    return '#22c55e';
                });

                new ApexCharts(document.querySelector("#risk-distribution"), {
                    chart: {
                        type: 'polarArea',
                        height: 320,
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const label = config.w.config.labels[config.dataPointIndex];
                                drillDown({
                                    type: 'risk',
                                    value: label
                                });
                            }
                        }
                    },
                    series: donutSeries,
                    labels: donutLabels,
                    colors: riskColors,
                    tooltip: {
                        theme: 'dark'
                    }
                }).render();
            } catch (e) {
                console.error('risk-distribution failed', e);
            }
        }

        if (!data.trend || !data.trend.series) {
            console.error('Trend data missing');
        }
        try {
            if (data.trend && data.trend.series && data.trend.categories) {
            new ApexCharts(document.querySelector("#engagement-trend"), {
                chart: {
                    type: 'line',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const rawDate = data.trend.categories[config.dataPointIndex];
                            drillDown({
                                type: 'date',
                                value: rawDate
                            });
                        }
                    }
                },
                series: data.trend.series,
                xaxis: {
                    categories: data.trend.categories.map(d => {
                        const date = new Date(d + 'T00:00:00');
                        return `${date.getDate()}/${date.getMonth()+1}`;
                    })
                },
                tooltip: {
                    theme: 'dark'
                }
            }).render();
            } else {
                console.error('Trend missing', data.trend);
            }
        } catch (e) {
            console.error('engagement-trend failed', e);
        }

        if (!data.house_breakdown || !data.house_breakdown.series) {
            console.error('House data missing');
        }
        try {
            if (data.house_breakdown && data.house_breakdown.series && data.house_breakdown.categories) {
            new ApexCharts(document.querySelector("#points-by-house"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = houseCategories[config.dataPointIndex];
                            drillDown({
                                type: 'house_low',
                                value: house
                            });
                        }
                    }
                },
                series: houseSeries,
                xaxis: {
                    categories: houseCategories
                },
                tooltip: {
                    theme: 'dark'
                }
            }).render();
            } else {
                console.error('House breakdown missing', data.house_breakdown);
            }
        } catch (e) {
            console.error('points-by-house failed', e);
        }
    }
    try {
        renderCharts(data);
    } catch (e) {
        console.error('PC CHART ERROR:', e);
    }

    modalClose.addEventListener('click', function () {
        modalBackdrop.style.display = 'none';
    });
    modalBackdrop.addEventListener('click', function (e) {
        if (e.target.id === 'pc-modal-backdrop') {
            modalBackdrop.style.display = 'none';
        }
    });
    document.getElementById('pc-apply').addEventListener('click', function () {
        try {
            renderCharts(data);
        } catch (e) {
            console.error('PC CHART ERROR:', e);
        }
    });
});
</script>
@endpush
