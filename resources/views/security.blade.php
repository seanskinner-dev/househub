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

</div>
@endsection
