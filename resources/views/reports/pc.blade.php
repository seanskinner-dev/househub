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
document.addEventListener("DOMContentLoaded", async function () {
    const dataUrl = @json(route('reports.data'));
    const drillUrl = @json(route('reports.drilldown'));
    const modalBackdrop = document.getElementById('pc-modal-backdrop');
    const modalClose = document.getElementById('pc-modal-close');
    const charts = { health: null, risk: null, trend: null, house: null };

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

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
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
                .map(k => `<th style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;">${escapeHtml(k)}</th>`)
                .join('');
            tbody.innerHTML = rows
                .map(row => `<tr style="border-bottom:1px solid #334155;">${keys.map(k => `<td style="padding:10px 12px;">${escapeHtml(row[k])}</td>`).join('')}</tr>`)
                .join('');
        }
        modalBackdrop.style.display = 'flex';
    }

    function drillDown(payload) {
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

    function handleDrill(label) {
        let type = 'low';
        if (!label) return;
        const l = label.toLowerCase();
        if (l.includes('high')) type = 'high';
        else if (l.includes('medium')) type = 'medium';
        else if (l.includes('low')) type = 'low';

        // Keep payload compatible with backend drilldown mapping.
        if (type === 'high' || type === 'medium') {
            drillDown({ type: 'risk_segment', value: label });
        } else {
            drillDown({ type: 'engagement_low', value: label });
        }
    }

    function destroyCharts() {
        Object.keys(charts).forEach(key => {
            if (charts[key]) {
                charts[key].destroy();
                charts[key] = null;
            }
        });
    }

    async function renderWithBackendData() {
        const res = await fetch(dataUrl + '?' + queryStringFromFilters(), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        console.log('PC DATA:', data);

        if (!data || !data.donut || !data.trend || !data.house_breakdown) {
            console.error('Missing data for charts');
            return;
        }

        destroyCharts();

        charts.health = new ApexCharts(document.querySelector("#engagement-health"), {
            chart: {
                type: 'donut',
                height: 320,
                events: {
                    dataPointSelection: (e, ctx, config) => {
                        const label = config.w.config.labels[config.dataPointIndex];
                        handleDrill(label);
                    }
                }
            },
            series: data.donut.series,
            labels: data.donut.labels,
            colors: ['#ef4444', '#eab308', '#22c55e']
        });
        charts.health.render();

        charts.risk = new ApexCharts(document.querySelector("#risk-distribution"), {
            chart: {
                type: 'polarArea',
                height: 320,
                events: {
                    dataPointSelection: (e, ctx, config) => {
                        const label = config.w.config.labels[config.dataPointIndex];
                        handleDrill(label);
                    }
                }
            },
            series: data.donut.series,
            labels: data.donut.labels,
            colors: ['#ef4444', '#eab308', '#22c55e']
        });
        charts.risk.render();

        charts.trend = new ApexCharts(document.querySelector("#engagement-trend"), {
            chart: {
                type: 'line',
                height: 320,
                events: {
                    dataPointSelection: (e, ctx, config) => {
                        const rawDate = (data.trend.categories || [])[config.dataPointIndex];
                        if (rawDate) {
                            drillDown({ type: 'date', value: rawDate });
                        }
                    }
                }
            },
            series: data.trend.series,
            xaxis: {
                categories: data.trend.categories.map(d => {
                    const date = new Date(d + 'T00:00:00');
                    return `${date.getDate()}/${date.getMonth() + 1}`;
                })
            },
            colors: ['#60a5fa']
        });
        charts.trend.render();

        charts.house = new ApexCharts(document.querySelector("#points-by-house"), {
            chart: {
                type: 'bar',
                height: 320,
                events: {
                    dataPointSelection: (e, ctx, config) => {
                        const label = (data.house_breakdown.categories || [])[config.dataPointIndex];
                        if (label) {
                            drillDown({ type: 'house_low', value: label });
                        }
                    }
                }
            },
            series: data.house_breakdown.series,
            xaxis: {
                categories: data.house_breakdown.categories
            },
            colors: ['#38bdf8']
        });
        charts.house.render();
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
        renderWithBackendData().catch(() => {});
    });

    renderWithBackendData().catch(() => {});
});
</script>
@endpush
