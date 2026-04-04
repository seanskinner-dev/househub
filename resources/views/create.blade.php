@extends('layouts.app')

@section('content')

<style>
    :root {
        --gryff: #7f0909; --slyth: #0d6217; --rave: #0e1a40; --huff: #ecb939;
        --nav-dark: #0f172a; --nav-med: #1e293b; --nav-light: #334155;
        --bg: #eef2f7; --success: #22c55e; --danger: #ef4444;
    }

    body { background: var(--bg); margin: 0; font-family: 'Inter', sans-serif; }

    .nav-top { background: var(--nav-dark); color: white; padding: 8px 30px; font-size: 12px; display: flex; justify-content: space-between; opacity: 0.9; }
    .nav-main { background: var(--nav-med); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .nav-admin { background: white; padding: 10px 30px; border-bottom: 1px solid #cbd5e1; display: flex; gap: 20px; font-size: 14px; font-weight: 600; color: var(--nav-med); }
    
    .nav-logo { font-size: 20px; font-weight: 900; letter-spacing: 1px; color: white; text-decoration: none; }
    .nav-link { color: var(--nav-med); text-decoration: none; padding: 5px 10px; border-radius: 5px; }
    .nav-link:hover { background: #f1f5f9; }

    .wrap { max-width: 900px; margin: 20px auto; padding: 0 20px; }

    .house-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .house-btn {
        width: 100%;
        padding: 18px 5px;
        font-size: 14px;
        font-weight: 800;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        color: white;
        transition: transform 0.1s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .house-btn:active { transform: scale(0.96); }

    .gryff { background: var(--gryff); }
    .slyth { background: var(--slyth); }
    .rave { background: var(--rave) !important; }
    .huffle { background: var(--huff); color: black; }

    .search {
        width: 100%;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        margin-bottom: 20px;
        font-size: 16px;
        box-sizing: border-box;
    }

    .card {
        background: white;
        border-radius: 16px;
        padding: 15px 20px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        border: 1px solid #e2e8f0;
    }

    .tally {
        background: #1e293b;
        color: white;
        min-width: 45px; height: 45px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 18px;
        margin-right: 20px;
    }

    .info { flex-grow: 1; }
    .name { font-weight: 800; font-size: 17px; display: block; }
    .meta { font-size: 13px; color: #64748b; font-weight: 600; }

    .actions { display: flex; gap: 8px; }
    
    .btn-math {
        border: none; border-radius: 10px;
        font-weight: 800; padding: 12px 18px;
        cursor: pointer; color: white;
    }
    .btn-minus { background: #fee2e2; color: var(--danger); border: 1px solid #fecaca; }
    .btn-minus:hover { background: var(--danger); color: white; }
    
    .btn-plus { background: var(--success); }
    .btn-plus:hover { filter: brightness(1.1); }

    .btn-icon {
        background: #f8fafc; border: 1px solid #e2e8f0;
        padding: 12px; border-radius: 10px; cursor: pointer;
        font-size: 16px;
    }

    .modal {
        display: none; position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.7);
        justify-content: center; align-items: center; z-index: 999;
    }
    .modal-box {
        background: white; padding: 30px; border-radius: 20px;
        width: 450px; box-shadow: 0 20px 25px rgba(0,0,0,0.2);
    }
</style>

<div class="nav-top">
    <span>SYSTEM STATUS: ACTIVE</span>
    <span>{{ now()->format('D d M, Y') }}</span>
</div>
<nav class="nav-main">
    <a href="#" class="nav-logo">HOUSEHUB</a>
    <div>{{ Auth::user()->name ?? 'Professor' }}</div>
</nav>
<div class="nav-admin">
    <a href="#" class="nav-link">Dashboard</a>
    <a href="#" class="nav-link" style="color: var(--success); border-bottom: 2px solid var(--success);">Awards Portal</a>
    <a href="#" class="nav-link">House Tally</a>
    <a href="#" class="nav-link">Logs</a>
</div>

<div class="wrap">
    <div class="house-bar">
        <form method="POST" action="{{ route('points.store') }}">
            @csrf
            <input type="hidden" name="house_id" value="1">
            <input type="hidden" name="amount" value="1">
            <input type="hidden" name="category" value="house">
            <button class="house-btn gryff">+1 Gryffindor</button>
        </form>

        <form method="POST" action="{{ route('points.store') }}">
            @csrf
            <input type="hidden" name="house_id" value="2">
            <input type="hidden" name="amount" value="1">
            <input type="hidden" name="category" value="house">
            <button class="house-btn slyth">+1 Slytherin</button>
        </form>

        <form method="POST" action="{{ route('points.store') }}">
            @csrf
            <input type="hidden" name="house_id" value="3">
            <input type="hidden" name="amount" value="1">
            <input type="hidden" name="category" value="house">
            <button class="house-btn rave">+1 Ravenclaw</button>
        </form>

        <form method="POST" action="{{ route('points.store') }}">
            @csrf
            <input type="hidden" name="house_id" value="4">
            <input type="hidden" name="amount" value="1">
            <input type="hidden" name="category" value="house">
            <button class="house-btn huffle">+1 Hufflepuff</button>
        </form>
    </div>

    <input class="search" type="text" id="search" placeholder="Search students or years...">

    <div id="student-list">
        @foreach($students as $student)
        <div class="card">
            <div class="tally">{{ $student->points ?? 0 }}</div>
            
            <div class="info">
                <span class="name">{{ $student->first_name }} {{ $student->last_name }}</span>
                <span class="meta">Year {{ $student->year_level }} • {{ $student->house_name }}</span>
            </div>

            <div class="actions">

                <!-- ✅ FIXED -->
                <form method="POST" action="{{ route('points.store') }}" class="persist-scroll">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="amount" value="-1">
                    <input type="hidden" name="category" value="manual">
                    <button class="btn-math btn-minus">-1</button>
                </form>

                <!-- ✅ FIXED -->
                <form method="POST" action="{{ route('points.store') }}" class="persist-scroll">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="amount" value="1">
                    <input type="hidden" name="category" value="manual">
                    <button class="btn-math btn-plus">+1</button>
                </form>

                <form method="POST" action="{{ route('commendations.store') }}" class="persist-scroll">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <button class="btn-icon">⭐</button>
                </form>

                <button type="button" class="btn-icon" onclick="openAwardModal({{ $student->id }}, '{{ $student->first_name }}')">🏆</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (localStorage.getItem("scrollPosition")) {
        window.scrollTo(0, localStorage.getItem("scrollPosition"));
        localStorage.removeItem("scrollPosition");
    }

    document.querySelectorAll('.persist-scroll').forEach(form => {
        form.addEventListener('submit', () => {
            localStorage.setItem("scrollPosition", window.scrollY);
        });
    });
});

document.getElementById('search').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    document.querySelectorAll('.card').forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(val) ? 'flex' : 'none';
    });
});
</script>

@endsection