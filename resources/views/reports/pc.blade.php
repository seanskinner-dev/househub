@extends('layouts.app')

@section('content')
    <h1 style="font-size: 2rem; margin-bottom: 1.25rem; font-weight: 700;">At-Risk Students</h1>
    <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 1.5rem; max-width: 48rem;">
        Students with no points in the last 30 days, including those who have never received a point transaction.
    </p>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 1.125rem; background: #1e293b; border-radius: 8px;">
            <thead>
                <tr style="border-bottom: 2px solid #334155;">
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">Name</th>
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">House</th>
                    <th style="text-align: right; padding: 16px 18px; font-size: 1.05rem;">Points (30 days)</th>
                    <th style="text-align: left; padding: 16px 18px; font-size: 1.05rem;">Last activity</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $row)
                    @php
                        $zeroPoints = ($row['points_last_30_days'] ?? 0) === 0;
                    @endphp
                    <tr style="border-bottom: 1px solid #334155; {{ $zeroPoints ? 'background: rgba(127, 29, 29, 0.45);' : '' }}">
                        <td style="padding: 16px 18px; font-weight: 600;">{{ $row['name'] }}</td>
                        <td style="padding: 16px 18px;">{{ $row['house'] }}</td>
                        <td style="padding: 16px 18px; text-align: right; font-variant-numeric: tabular-nums;">
                            {{ number_format($row['points_last_30_days']) }}
                        </td>
                        <td style="padding: 16px 18px;">
                            @if ($row['last_activity'] === null)
                                <span style="opacity: 0.85;">—</span>
                            @else
                                {{ $row['last_activity'] }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 24px 18px; text-align: center; opacity: 0.9;">
                            No at-risk students found (everyone has earned points in the last 30 days).
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
