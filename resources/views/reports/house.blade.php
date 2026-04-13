@extends('layouts.app')

@section('content')
    <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">House Performance Report</h1>

    <details style="margin-bottom: 1rem; opacity: 0.85;">
        <summary style="cursor: pointer;">Raw <code>$housePerformance</code> (debug)</summary>
        <pre style="background: #1e293b; padding: 12px; overflow: auto; max-height: 240px; font-size: 12px;">{{ print_r($housePerformance, true) }}</pre>
    </details>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: #1e293b; border-radius: 8px;">
            <thead>
                <tr style="border-bottom: 1px solid #334155;">
                    <th style="text-align: left; padding: 10px 12px;">House</th>
                    <th style="text-align: right; padding: 10px 12px;">Year total</th>
                    <th style="text-align: right; padding: 10px 12px;">This term</th>
                    <th style="text-align: right; padding: 10px 12px;">Previous term</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($housePerformance as $house)
                    <tr style="border-bottom: 1px solid #334155;">
                        <td style="padding: 10px 12px; font-weight: 600;">{{ $house['house'] }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['year_total']) }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['term_total']) }}</td>
                        <td style="padding: 10px 12px; text-align: right;">{{ number_format($house['last_term_total']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
