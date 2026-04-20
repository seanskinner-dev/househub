@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 40px auto; padding: 20px;">

    <h1>Privacy Policy</h1>

    <p><strong>Last updated:</strong> {{ now()->format('F Y') }}</p>

    <h2>What data we collect</h2>
    <p>HouseHub stores student names, house points, awards, and engagement activity for the purpose of supporting school operations.</p>

    <h2>Purpose of data</h2>
    <p>This data is used to support student engagement, participation tracking, and internal reporting within schools.</p>

    <h2>Data storage</h2>
    <p>All data is hosted within Australia (Sydney-based infrastructure).</p>

    <h2>Access control</h2>
    <p>Data is only accessible to authorised users within the organisation.</p>

    <h2>Data retention</h2>
    <p>Data is retained only as long as required by the school.</p>

    <h2>Contact</h2>
    <p>For any privacy-related enquiries, contact: sean@househub.net.au</p>

</div>
@endsection
