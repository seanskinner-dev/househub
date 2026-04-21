@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background:#1e293b;color:#f1f5f9;">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-white-50 mb-2">Total Actions</h2>
                    <div class="display-6 mb-0">{{ number_format($totalActions) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="background:#1e293b;color:#f1f5f9;">
                <div class="card-body">
                    <h2 class="h6 text-uppercase text-white-50 mb-2">Unique Users</h2>
                    <div class="display-6 mb-0">{{ number_format($uniqueUsers) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background:#1e293b;color:#f1f5f9;">
                <div class="card-header border-secondary" style="background:#0f172a;border-color:#334155 !important;">
                    Daily Activity (Last 14 Days)
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($daily as $day)
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background:#1e293b;color:#f1f5f9;border-color:#334155;">
                            <span>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</span>
                            <span>{{ number_format($day->total) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item" style="background:#1e293b;color:#94a3b8;border-color:#334155;">
                            No activity found.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="background:#1e293b;color:#f1f5f9;">
                <div class="card-header border-secondary" style="background:#0f172a;border-color:#334155 !important;">
                    Top Users
                </div>
                <ul class="list-group list-group-flush">
                    @forelse ($topUsers as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="background:#1e293b;color:#f1f5f9;border-color:#334155;">
                            <span>{{ $user->name }}</span>
                            <span>{{ number_format($user->total) }}</span>
                        </li>
                    @empty
                        <li class="list-group-item" style="background:#1e293b;color:#94a3b8;border-color:#334155;">
                            No users found.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
