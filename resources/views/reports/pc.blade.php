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
                <div class="card mb-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5>Engagement Health</h5>
                        <p class="small text-white-50">
                            Shows the proportion of students actively receiving points versus those at risk. Focus on reducing the high-risk group to improve overall engagement.
                        </p>
                        <div id="engagement-health"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5>Year Level Distribution</h5>
                        <p class="small text-white-50">
                            Shows student counts across year levels. Click a year to see student-level details.
                        </p>
                        <div id="year-level-distribution"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5>Engagement Trend</h5>
                        <p class="small text-white-50">
                            Displays how many points are awarded each day over the selected period. Sudden drops highlight days where student engagement is low.
                        </p>
                        <div id="engagement-trend"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4 shadow-sm h-100">
                    <div class="card-body">
                        <h5>Points by House</h5>
                        <p class="small text-white-50">
                            Compares total points earned by each house to identify engagement differences. Lower-performing houses may need targeted support.
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
    const data = @json($data ?? null);
    window.pcData = data;
    console.log('DATA FROM BACKEND:', data);
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (typeof ApexCharts === 'undefined') return;

    const drillUrl = @json(route('reports.drilldown'));
    const dataUrl = @json(route('reports.data'));
    const modalBackdrop = document.getElementById('pc-modal-backdrop');
    const modalClose = document.getElementById('pc-modal-close');
    const instances = {};

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

    function normalizeSeries(dataset) {
        if (!dataset || !Array.isArray(dataset.series) || dataset.series.length === 0) {
            return [];
        }
        const first = dataset.series[0];
        if (Array.isArray(first?.data)) {
            return first.data.map(v => Number(v) || 0);
        }
        return dataset.series.map(v => Number(v) || 0);
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
            theadRow.innerHTML = keys.map(k => `<th style="text-align:left;padding:10px 12px;border-bottom:2px solid #334155;">${String(k)}</th>`).join('');
            tbody.innerHTML = rows.map(row => `<tr style="border-bottom:1px solid #334155;">${keys.map(k => `<td style="padding:10px 12px;">${String(row[k] ?? '')}</td>`).join('')}</tr>`).join('');
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
        }).then(res => res.json()).then(renderDrillDownModal).catch(() => {});
    }

    function showEmpty(containerId) {
        const el = document.getElementById(containerId);
        if (el) {
            el.innerHTML = '<div class="text-white-50 small">No data available</div>';
        }
    }

    function destroyChart(containerId) {
        if (instances[containerId]) {
            instances[containerId].destroy();
            instances[containerId] = null;
        }
        const el = document.getElementById(containerId);
        if (el) el.innerHTML = '';
    }

    const drilldownMap = {
        'engagement-health': 'risk_segment',
        'points-by-house': 'house',
        'year-level-distribution': 'year_level'
    };

    function renderChart(id, data, config) {
        const containerId = id;
        destroyChart(containerId);
        if (!data?.series || !data?.series[0] || !data?.series[0]?.data) {
            console.warn('Invalid series structure', id, data);
            return;
        }
        if ((data.categories || []).length !== data.series[0].data.length) {
            console.warn('Mismatch categories vs data', id, data);
        }

        console.log('Rendering chart:', id, {
            categories: data.categories?.length,
            series: data.series?.[0]?.data?.length
        });

        const categories = Array.isArray(data?.categories) ? data.categories : [];
        const dataPoints = normalizeSeries(data);

        if (categories.length === 0 || dataPoints.length === 0) {
            showEmpty(containerId);
            return;
        }

        const options = {
            chart: {
                type: config.type,
                height: 320,
                events: drilldownMap[containerId] ? {
                    dataPointSelection: function (event, chartContext, cfg) {
                        const value = categories[cfg.dataPointIndex];
                        if (!value) return;
                        const mappedType = drilldownMap[containerId] === 'house' ? 'house_low' : drilldownMap[containerId];
                        drillDown({ type: mappedType, value: value });
                    }
                } : {}
            },
            tooltip: { theme: 'dark' },
            series: config.type === 'donut'
                ? dataPoints
                : [{ name: (data.series?.[0]?.name || 'Value'), data: dataPoints }],
            xaxis: config.type === 'donut' ? undefined : { categories: categories },
            labels: config.type === 'donut' ? categories : undefined,
            colors: config.colors || undefined
        };

        instances[containerId] = new ApexCharts(document.querySelector('#' + containerId), options);
        instances[containerId].render();
    }

    async function loadAndRender() {
        const res = await fetch(dataUrl + '?' + queryStringFromFilters(), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const payload = await res.json();
        const data = payload;
        console.log('FULL DATA OBJECT:', data);

        const risk = payload.risk_distribution || { type: 'breakdown', categories: [], series: [] };
        const trend = payload.engagement_trend || { type: 'trend', categories: [], series: [] };
        const yearSeries = data?.year_level?.series?.[0]?.data || data?.year_level_distribution?.series?.[0]?.data || [];
        const yearCategories = data?.year_level?.categories || data?.year_level_distribution?.categories || [];
        const houseSeries = data?.house_breakdown?.series?.[0]?.data || data?.points_by_house?.series?.[0]?.data || [];
        const houseCategories = data?.house_breakdown?.categories || data?.points_by_house?.categories || [];

        if (yearSeries.length === 0) {
            console.error('Year level data missing or empty', data.year_level || data.year_level_distribution);
        }
        if (houseSeries.length === 0) {
            console.error('House data missing or empty', data.house_breakdown || data.points_by_house);
        }

        const riskColors = risk.categories.map(label => {
            const l = String(label).toLowerCase();
            if (l.includes('high')) return '#ef4444';
            if (l.includes('medium')) return '#eab308';
            return '#22c55e';
        });

        renderChart('engagement-health', risk, { type: 'donut', colors: riskColors });
        renderChart('engagement-trend', trend, { type: 'line' });

        destroyChart('points-by-house');
        if (houseSeries.length > 0) {
            instances['points-by-house'] = new ApexCharts(document.querySelector("#points-by-house"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const house = houseCategories[config.dataPointIndex];
                            if (!house) return;
                            drillDown({
                                type: 'house_low',
                                value: house
                            });
                        }
                    }
                },
                series: [{ data: houseSeries }],
                xaxis: { categories: houseCategories },
                tooltip: { theme: 'dark' }
            });
            instances['points-by-house'].render();
        } else {
            showEmpty('points-by-house');
        }

        destroyChart('year-level-distribution');
        if (yearSeries.length > 0) {
            instances['year-level-distribution'] = new ApexCharts(document.querySelector("#year-level-distribution"), {
                chart: {
                    type: 'bar',
                    height: 320,
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            const year = yearCategories[config.dataPointIndex];
                            if (!year) return;
                            drillDown({
                                type: 'year_level',
                                value: year
                            });
                        }
                    }
                },
                series: [{ data: yearSeries }],
                xaxis: { categories: yearCategories },
                tooltip: { theme: 'dark' }
            });
            instances['year-level-distribution'].render();
        } else {
            showEmpty('year-level-distribution');
        }
    }

    modalClose.addEventListener('click', function () {
        modalBackdrop.style.display = 'none';
    });
    modalBackdrop.addEventListener('click', function (e) {
        if (e.target.id === 'pc-modal-backdrop') modalBackdrop.style.display = 'none';
    });
    document.getElementById('pc-apply').addEventListener('click', function () {
        loadAndRender().catch(err => console.error('PC load failed', err));
    });

    loadAndRender().catch(err => console.error('PC load failed', err));
});
</script>
@endpush
