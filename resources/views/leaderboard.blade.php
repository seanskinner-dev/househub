@extends('layouts.app')

@section('content')
<style>
    body { background:#eef2f7; font-family: 'Segoe UI', sans-serif; }
    .wrap { max-width:820px; margin:20px auto; }
    .header { font-size:24px; font-weight:bold; margin-bottom:15px; }

    /* HOUSE BUTTONS */
    .house-bar { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:15px; }
    .house-btn { width:100%; padding:20px; font-size:18px; font-weight:bold; border:none; border-radius:12px; cursor:pointer; color:white; }
    .gryff { background:#7f0909; }
    .slyth { background:#0d6217; }
    .raven { background:#0e1a40; }
    .huffle { background:#ecb939; color:black; }

    /* SEARCH & CARDS */
    .search { width:100%; padding:12px; border-radius:10px; border:1px solid #ddd; margin-bottom:12px; box-sizing: border-box; }
    .card { position:relative; padding:20px; margin-bottom:10px; border-radius:12px; background:white; display:flex; align-items:center; justify-content:space-between; }
    .name { font-weight:bold; font-size:18px; }
    .meta { font-size:13px; color:#666; }
    .pts-badge { background:#f0f4f8; padding:2px 8px; border-radius:10px; font-weight:bold; margin-left:5px; color:#2d3748; }

    /* ACTIONS */
    .actions { display:flex; gap:12px; }
    .point-btn { padding:16px 24px; font-size:18px; font-weight:bold; border:none; border-radius:14px; cursor:pointer; color:white; }
    .star { padding:16px 18px; border:none; border-radius:14px; background:#444; color:white; font-size:18px; cursor:pointer; }
    
    /* MODAL */
    .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:999; }
    .modal-box { background:white; padding:25px; border-radius:15px; width:400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    select, textarea { width:100%; padding:10px; margin:10px 0; border:1px solid #ddd; border-radius:8px; }
</style>

<div class="wrap">
    <div class="header">Award Points</div>

    <div class="house-bar">
        @foreach($houses as $house)
            @php 
                $class = match($house->id) { 1=>'gryff', 2=>'slyth', 3=>'raven', 4=>'huffle', default=>'' };
            @endphp
            <form method="POST" action="{{ route('points.store') }}">
                @csrf
                <input type="hidden" name="house_name" value="{{ $house->name }}">
                <input type="hidden" name="amount" value="1">
                <button class="house-btn {{ $class }}">+1 {{ $house->name }}</button>
            </form>
        @endforeach
    </div>

    <input class="search" type="text" id="search" placeholder="Search students...">

    <div id="student-list">
        @foreach($students as $student)
        <div class="card">
            <div>
                <div class="name">
                    {{ $student->first_name }} {{ $student->last_name }}
                    <span class="pts-badge">{{ $student->house_points ?? 0 }} pts</span>
                </div>
                <div class="meta">Y{{ $student->year_level }} • {{ $student->house_name }}</div>
            </div>

            <div class="actions">
                <form method="POST" action="{{ route('points.store') }}">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <input type="hidden" name="amount" value="1">
                    <button class="point-btn" style="background:{{ $student->colour_hex }}">+1</button>
                </form>

                <form method="POST" action="{{ route('commendations.store') }}">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    <button class="star">⭐</button>
                </form>

                <button type="button" class="star" onclick="openAwardModal({{ $student->id }}, '{{ $student->first_name }}')">🏆</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div id="awardModal" class="modal">
    <div class="modal-box">
        <h3 id="modalTitle" style="margin-top:0">Issue Award</h3>
        <label>Select Achievement</label>
        <select id="award_name">
            <option value="Order of Merlin">Order of Merlin (First Class)</option>
            <option value="Special Award for Services to the School">Special Award for Services</option>
            <option value="Quidditch Cup Contribution">Quidditch Cup Contribution</option>
            <option value="Advanced Potion-Making Excellence">Advanced Potion-Making</option>
            <option value="Winning the House Cup">Contribution to House Cup</option>
        </select>
        <textarea id="award_description" placeholder="Why are they receiving this?"></textarea>
        <div style="display:flex; gap:10px; margin-top:10px;">
            <button onclick="submitAward()" style="flex:2; padding:12px; background:#48bb78; color:white; border:none; border-radius:8px; cursor:pointer;">Submit</button>
            <button onclick="closeAwardModal()" style="flex:1; padding:12px; background:#eee; border:none; border-radius:8px; cursor:pointer;">Cancel</button>
        </div>
    </div>
</div>

<script>
let currentId = null;

function openAwardModal(id, name) {
    currentId = id;
    document.getElementById('modalTitle').innerText = "Award for " + name;
    document.getElementById('awardModal').style.display = 'flex';
}

function closeAwardModal() { document.getElementById('awardModal').style.display = 'none'; }

function submitAward() {
    const name = document.getElementById('award_name').value;
    const desc = document.getElementById('award_description').value;

    if(!desc) return alert("Please enter a description.");

    fetch("{{ route('award.store') }}", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        body: JSON.stringify({ student_id: currentId, award_name: name, award_description: desc })
    }).then(res => {
        closeAwardModal();
        alert("Award Issued Successfully!");
    });
}

document.getElementById('search').addEventListener('keyup', function() {
    let val = this.value.toLowerCase();
    document.querySelectorAll('.card').forEach(card => {
        card.style.display = card.innerText.toLowerCase().includes(val) ? 'flex' : 'none';
    });
});
</script>
@endsection