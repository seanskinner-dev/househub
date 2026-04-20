@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 40px auto; padding: 20px;">

    <h1>Data Security</h1>

    <p><strong>Last updated:</strong> {{ now()->format('F Y') }}</p>

    <h2>Authentication</h2>
    <p>Access to HouseHub is protected with user login requirements.</p>

    <h2>Access control</h2>
    <p>Data is available only to authorised users within the organisation.</p>

    <h2>Data hosting</h2>
    <p>All data is hosted in Sydney, Australia.</p>

    <h2>Backups</h2>
    <p>Regular backups are performed to support data protection and continuity.</p>

    <h2>Contact for security issues</h2>
    <p>For security-related enquiries, contact: sean@househub.net.au</p>

    <h2>Incident Response</h2>
    <p>In the event of a security incident or data breach, HouseHub will take immediate steps to contain the issue and notify affected parties as soon as practicable.</p>

    <h2>Vulnerability Disclosure</h2>
    <p>If you identify a potential security issue, please report it to sean@househub.net.au. All reports will be investigated and addressed promptly.</p>

</div>
@endsection
