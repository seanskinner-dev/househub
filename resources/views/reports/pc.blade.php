@extends('layouts.app')

@section('content')
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

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Risk distribution</h2>
        <div style="max-width: 460px;">
            <div id="risk-distribution"></div>
        </div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Engagement trend (weekdays in range)</h2>
        <div id="engagement-trend" style="min-height: 400px;"></div>
    </section>

    <section style="margin-bottom: 3rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by house</h2>
        <div id="points-by-house" style="min-height: 420px;"></div>
    </section>

    <section style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.35rem; margin-bottom: 1rem; font-weight: 600;">Points by year level</h2>
        <div id="engagement-health" style="min-height: 420px;"></div>
    </section>

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

            // TEST DATA (temporary to confirm rendering works)
            const names = ['Gryffindor', 'Slytherin', 'Ravenclaw', 'Hufflepuff'];
            const values = [120, 150, 90, 70];

            //-----------------------------------
            // ENGAGEMENT HEALTH
            //-----------------------------------
            new ApexCharts(document.querySelector("#engagement-health"), {
                chart: { type: 'donut' },
                series: values,
                labels: names,
                title: { text: 'Engagement Health' }
            }).render();

            //-----------------------------------
            // ENGAGEMENT TREND
            //-----------------------------------
            new ApexCharts(document.querySelector("#engagement-trend"), {
                chart: { type: 'line' },
                series: [{ data: values }],
                xaxis: { categories: names },
                title: { text: 'Engagement Trend' }
            }).render();

            //-----------------------------------
            // POINTS BY HOUSE
            //-----------------------------------
            new ApexCharts(document.querySelector("#points-by-house"), {
                chart: { type: 'bar' },
                series: [{ data: values }],
                xaxis: { categories: names },
                title: { text: 'Points by House' }
            }).render();

            //-----------------------------------
            // RISK DISTRIBUTION
            //-----------------------------------
            new ApexCharts(document.querySelector("#risk-distribution"), {
                chart: { type: 'polarArea' },
                series: values,
                labels: names,
                title: { text: 'Risk Distribution' }
            }).render();

        });
    </script>
@endpush
