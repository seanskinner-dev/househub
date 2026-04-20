@extends('layouts.app')

@section('content')
<div style="max-width: 800px; margin: 40px auto; padding: 20px;">

    <h1>Terms of Service</h1>

    <p><strong>Last updated:</strong> {{ now()->format('F Y') }}</p>

    <h2>Service Overview</h2>
    <p>HouseHub provides a platform for schools to manage house points, student engagement, and participation tracking.</p>

    <h2>Use of Service</h2>
    <p>The platform is intended for use by authorised school staff. Schools are responsible for managing user access and ensuring appropriate use.</p>

    <h2>Data Responsibility</h2>
    <p>Schools retain ownership and responsibility for all data entered into the platform.</p>

    <h2>Availability</h2>
    <p>The service is provided on an "as-is" basis. While efforts are made to ensure reliability, uninterrupted availability is not guaranteed.</p>

    <h2>Limitation of Liability</h2>
    <p>HouseHub is not liable for any indirect or consequential loss arising from the use of the platform.</p>

    <h2>Contact</h2>
    <p>For enquiries, contact: sean@househub.net.au</p>

</div>
@endsection
