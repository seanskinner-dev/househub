<!DOCTYPE html>
<html>
<head>
    <title>Award Points</title>

    <style>
        body {
            font-family: Inter, system-ui;
            background:#2b2f36;
            margin:0;
            padding:20px;
        }

        .layout {
            display:flex;
            gap:20px;
            max-width:1200px;
            margin:auto;
        }

        .main { flex:2; }

        .sidebar {
            flex:1;
            position:sticky;
            top:20px;
        }

        h1 { color:white; }

        .house-bar {
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:10px;
            margin-bottom:15px;
        }

        .house-btn {
            width:100%;
            padding:18px 0;
            font-weight:800;
            border:none;
            border-radius:10px;
            cursor:pointer;
            font-size:16px;
        }

        .gryffindor { background:#740001; color:white; }
        .slytherin { background:#1a472a; color:white; }
        .ravenclaw { background:#0e1a40; color:white; }
        .hufflepuff { background:#ffcc00; color:#111; }

        #search {
            width:100%;
            padding:12px;
            border-radius:8px;
            border:none;
            margin-bottom:15px;
            background:#3a3f47;
            color:white;
        }

        .card {
            background:#f3f4f6;
            padding:18px;
            border-radius:10px;
            margin-bottom:10px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .left {
            display:flex;
            align-items:center;
            gap:16px;
        }

        .crest {
            width:64px;
            filter: drop-shadow(0 0 8px rgba(255,255,255,0.35));
            transition: transform 0.15s ease;
        }

        .card:hover .crest {
            transform: scale(1.08);
        }

        .name {
            font-weight:800;
            font-size:20px;
        }

        .meta {
            font-size:12px;
            color:#555;
        }

        .actions {
            display:flex;
            gap:8px;
        }

        .btn {
            border:none;
            border-radius:10px;
            cursor:pointer;
            font-weight:800;
        }

        .plus {
            background:#22c55e;
            color:white;
            padding:18px 22px;
            font-size:18px;
        }

        .minus {
            background:#ef4444;
            color:white;
            padding:10px 14px;
            font-size:14px;
        }

        .star { background:#4b5563; color:white; padding:10px; }
        .award { background:#6366f1; color:white; padding:10px; }

        .sidebar-box {
            background:#1f2329;
            padding:12px;
            border-radius:10px;
            color:white;
        }

        .activity {
            background:#3a4048;
            padding:12px;
            margin-bottom:8px;
            border-radius:8px;
            font-size:13px;
        }

        .pos { color:#22c55e; }
        .neg { color:#ef4444; }

        /* MODAL */
        .modal {
            position: fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background: rgba(0,0,0,0.6);
            display:none;
            align-items:center;
            justify-content:center;
        }

        .modal-box {
            background:#1f2329;
            padding:20px;
            border-radius:12px;
            width:420px;
            color:white;
        }

        .modal textarea, .modal select {
            width:100%;
            padding:12px;
            margin-top:10px;
            border-radius:8px;
            border:none;
            background:#2f343c;
            color:white;
            font-size:14px;
        }

        textarea {
            min-height:120px;
        }

        .modal button {
            margin-top:10px;
            padding:10px;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        #toast {
            position:fixed;
            top:20px;
            right:20px;
            padding:12px 18px;
            border-radius:10px;
            opacity:0;
            transition:0.3s;
            color:white;
        }

        #toast.show { opacity:1; }
        .toast-success { background:#22c55e; }
        .toast-error { background:#ef4444; }

    </style>
</head>

<body>

<div class="layout">

<div class="main">

<h1>Award Points</h1>

<div class="house-bar">
@foreach(['Gryffindor','Slytherin','Ravenclaw','Hufflepuff'] as $house)
<form method="POST" action="{{ route('points.store') }}">
@csrf
<input type="hidden" name="house_name" value="{{ $house }}">
<input type="hidden" name="points" value="1">
<button class="house-btn {{ strtolower($house) }}">+1 {{ $house }}</button>
</form>
@endforeach
</div>

<input id="search" placeholder="Search student...">

@foreach($students as $student)
<div class="card student-card">

<div class="left">
<img class="crest" src="/images/{{ strtolower($student->house_name) }}.png">

<div>
<div class="name">{{ $student->first_name }} {{ $student->last_name }}</div>
<div class="meta">{{ $student->house_name }} • {{ $student->house_points }} pts</div>
</div>
</div>

<div class="actions">

<form method="POST" action="{{ route('points.store') }}">
@csrf
<input type="hidden" name="student_id" value="{{ $student->id }}">
<input type="hidden" name="points" value="-1">
<button class="btn minus" onclick="toast(-1)">-1</button>
</form>

<form method="POST" action="{{ route('points.store') }}">
@csrf
<input type="hidden" name="student_id" value="{{ $student->id }}">
<input type="hidden" name="points" value="1">
<button class="btn plus" onclick="toast(1)">+1</button>
</form>

<button class="btn star" onclick="openCommendation({{ $student->id }})">⭐</button>
<button class="btn award" onclick="openAward({{ $student->id }})">🏆</button>

</div>

</div>
@endforeach

</div>

<div class="sidebar">
<div class="sidebar-box">
<h3>Recent</h3>

@foreach($recent as $r)
<div class="activity">

@if($r->category === 'house')
<strong>{{ $r->house_name }}</strong>
@else
<strong>{{ $r->first_name }} {{ $r->last_name }}</strong>
@endif

<span class="{{ $r->amount > 0 ? 'pos' : 'neg' }}">
{{ $r->amount > 0 ? '+' : '' }}{{ $r->amount }}
</span>

<div style="font-size:11px;opacity:0.6;">
{{ $r->teacher ?? 'System' }}
</div>

</div>
@endforeach

</div>
</div>

</div>

<!-- ⭐ COMMENDATION -->
<div id="commendationModal" class="modal">
<div class="modal-box">
<form method="POST" action="{{ route('points.store') }}">
@csrf
<input type="hidden" name="student_id" id="commStudent">
<input type="hidden" name="points" value="1">
<input type="hidden" name="category" value="commendation">
<textarea name="description" placeholder="What did they do well?" required></textarea>
<button type="submit">Save</button>
<button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>
</div>

<!-- 🏆 AWARD -->
<div id="awardModal" class="modal">
<div class="modal-box">
<form method="POST" action="{{ route('points.store') }}">
@csrf
<input type="hidden" name="student_id" id="awardStudent">
<input type="hidden" name="category" value="award">
<input type="hidden" name="points" id="awardPoints">

<select id="awardSelect" onchange="setAwardPoints()">
<option value="">Select Award</option>
<option value="5">House Pride (+5)</option>
<option value="10">Outstanding Effort (+10)</option>
<option value="15">Professor’s Recognition (+15)</option>
<option value="20">Order of Merlin (+20)</option>
</select>

<textarea name="description" placeholder="Optional notes..."></textarea>

<button type="submit">Save</button>
<button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>
</div>

<div id="toast"></div>

<script>
function toast(type){
let t=document.getElementById('toast');
t.className='show '+(type>0?'toast-success':'toast-error');
t.innerText=type>0?'+1 Point':'-1 Point';
setTimeout(()=>t.className='',1200);
}

function openCommendation(id){
document.getElementById('commStudent').value=id;
document.getElementById('commendationModal').style.display='flex';
}

function openAward(id){
document.getElementById('awardStudent').value=id;
document.getElementById('awardModal').style.display='flex';
}

function closeModal(){
document.getElementById('commendationModal').style.display='none';
document.getElementById('awardModal').style.display='none';
}

function setAwardPoints(){
let val=document.getElementById('awardSelect').value;
document.getElementById('awardPoints').value=val;
}

document.getElementById('search').addEventListener('keyup',function(){
let v=this.value.toLowerCase();
document.querySelectorAll('.student-card').forEach(c=>{
c.style.display=c.innerText.toLowerCase().includes(v)?'':'none';
});
});
</script>

</body>
</html>a