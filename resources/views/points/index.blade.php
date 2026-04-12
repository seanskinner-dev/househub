@extends('layouts.app')

@section('content')

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

        .house-section {
            background:#3a3f47;
            padding:18px;
            border-radius:14px;
            margin-bottom:20px;
        }

        .house-bar {
            display:grid;
            grid-template-columns:repeat(4,1fr);
            gap:14px;
        }

        .house-card {
            background:linear-gradient(
                145deg,
                var(--colour) 0%,
                rgba(0,0,0,0.4) 100%
            );
            border-radius:16px;
            padding:20px;
            text-align:center;
            cursor:pointer;
            border:none;
        }

        .house-emoji {
            font-size:64px;
            display:block;
            margin-bottom:10px;
        }

        .house-name {
            font-weight:800;
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
            font-size:32px;
        }

        .name {
            font-weight:800;
            font-size:20px;
        }

        .student-link {
            text-decoration:none;
            color:inherit;
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

        .plus { background:#22c55e; color:white; padding:18px; }
        .minus { background:#ef4444; color:white; padding:10px; }
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
        }

        .pos { color:#22c55e; }
        .neg { color:#ef4444; }

        #toast {
            position:fixed;
            bottom:30px;
            right:30px;
            padding:14px 20px;
            border-radius:10px;
            opacity:0;
            color:white;
            font-weight:700;
        }

        #toast.show { opacity:1; }
        .toast-success { background:#22c55e; }
        .toast-error { background:#ef4444; }

        .modal {
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.7);
            display:none;
            align-items:center;
            justify-content:center;
        }

        .modal-box {
            background:#1f2329;
            padding:30px;
            border-radius:16px;
            width:600px;
            color:white;
        }

        textarea {
            width:100%;
            height:260px;
            margin-top:10px;
        }

        label {
            display:block;
            margin-top:10px;
            margin-bottom:5px;
            font-weight:800;
        }
    </style>
</head>

<body>

<div class="layout">

<div class="main">

<h1>Award Points</h1>

<div class="house-section">
<div class="house-bar">

@foreach($houses as $house)
<form method="POST" action="{{ route('points.store') }}" class="ajax">
@csrf
<input type="hidden" name="house_name" value="{{ $house->name }}">
<input type="hidden" name="amount" value="1">

<div class="house-card" style="--colour: {{ $house->colour_hex }}">
    <div class="house-emoji">
        @if($house->name == 'Gryffindor') 🦁
        @elseif($house->name == 'Slytherin') 🐍
        @elseif($house->name == 'Ravenclaw') 🦅
        @elseif($house->name == 'Hufflepuff') 🦡
        @else ❓
        @endif
    </div>
    <div class="house-name">{{ $house->name }}</div>
</div>
</form>
@endforeach

</div>
</div>

<input id="studentSearch" placeholder="Search students..." style="width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:none;">

@foreach(collect($students)->sortBy('first_name') as $student)
<div class="card">

<div class="left">
<div class="crest">
    @if(($student->house_name ?? '') == 'Gryffindor') 🦁
    @elseif(($student->house_name ?? '') == 'Slytherin') 🐍
    @elseif(($student->house_name ?? '') == 'Ravenclaw') 🦅
    @elseif(($student->house_name ?? '') == 'Hufflepuff') 🦡
    @else ❓
    @endif
</div>

<div>
<div class="name">
<a href="{{ route('students.show', $student->id) }}" class="student-link">
{{ $student->first_name }} {{ $student->last_name }}
</a>
</div>
<div class="meta">
{{ $student->house_name ?? 'Unknown' }} • {{ $student->house_points }} pts
</div>
</div>
</div>

<div class="actions">

<form method="POST" action="{{ route('points.store') }}" class="ajax">
@csrf
<input type="hidden" name="student_id" value="{{ $student->id }}">
<input type="hidden" name="amount" value="-1">
<button class="btn minus">-1</button>
</form>

<form method="POST" action="{{ route('points.store') }}" class="ajax">
@csrf
<input type="hidden" name="student_id" value="{{ $student->id }}">
<input type="hidden" name="amount" value="1">
<button class="btn plus">+1</button>
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
<div id="recentList"></div>
</div>
</div>

</div>

<div id="toast"></div>

<!-- EXISTING MODALS -->

<div id="commendationModal" class="modal">
<div class="modal-box">
<form method="POST" class="ajax">
@csrf
<input type="hidden" name="student_id" id="commStudent">
<input type="hidden" name="amount" value="1">
<input type="hidden" name="type" value="commendation">

<textarea name="description">Write a strong reason. Minimum 150 words.</textarea>

<button>Save</button>
<button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>
</div>

<div id="awardModal" class="modal">
<div class="modal-box">
<form method="POST" class="ajax">
@csrf
<input type="hidden" name="student_id" id="awardStudent">
<input type="hidden" name="amount" id="awardPoints">
<input type="hidden" name="type" value="award">

<select id="awardSelect" onchange="setAwardDetails()">
<option data-points="10" selected>Prefect Recognition</option>
<option data-points="15">Outstanding Magical Effort</option>
<option data-points="20">Professor’s Excellence Award</option>
<option data-points="25">Headmaster’s Honour</option>
<option data-points="30">Order of Merlin</option>
</select>

<textarea name="description">Describe why this award is given.</textarea>

<button>Save</button>
<button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

document.querySelectorAll('.house-card').forEach(card=>{
card.addEventListener('click',function(){
    this.closest('form').dispatchEvent(new Event('submit',{cancelable:true}));
});
});

window.openCommendation = function(id){
    document.getElementById('commStudent').value = id;
    document.getElementById('commendationModal').style.display = 'flex';
};

window.openAward = function(id){
    document.getElementById('awardStudent').value = id;

    let sel=document.getElementById('awardSelect');
    let pts=sel.options[sel.selectedIndex].getAttribute('data-points');
    document.getElementById('awardPoints').value = pts;

    document.getElementById('awardModal').style.display = 'flex';
};

window.closeModal = function(){
    document.getElementById('commendationModal').style.display = 'none';
    document.getElementById('awardModal').style.display = 'none';
};

window.setAwardDetails = function(){
    let sel=document.getElementById('awardSelect');
    let pts=sel.options[sel.selectedIndex].getAttribute('data-points');
    document.getElementById('awardPoints').value = pts;
};

document.querySelectorAll('form.ajax').forEach(form=>{
form.addEventListener('submit',function(e){
e.preventDefault();

let fd=new FormData(this);
let fallback=parseInt(fd.get('amount'))||0;
let type=fd.get('type');
let houseName=fd.get('house_name');

if(type==='commendation'){
let words=(fd.get('description')||'').trim().split(/\s+/).length;
if(words<150){alert('150 words required');return;}
}

fetch('/points',{method:'POST',headers:{
'X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value,
'Accept':'application/json'
},body:fd})
.then(r=>r.json())
.then(data=>{

let amt=Number(data.amount);
if(isNaN(amt)) amt=fallback;

let label=data.student||data.house||houseName||'Student';

let typeLabel='Points';
if(type==='commendation') typeLabel='Commendation';
if(type==='award') typeLabel='Award';
if(houseName) typeLabel='House points';

let t=document.getElementById('toast');
t.className='show '+(amt>0?'toast-success':'toast-error');
t.innerText=(amt>0?'+ ':'')+amt;
setTimeout(()=>t.className='',1000);

let list=document.getElementById('recentList');
let item=document.createElement('div');
item.className='activity';
item.innerHTML=`<strong>${label}</strong><br>
<span class="${amt>0?'pos':'neg'}">${amt}</span>
<br><small>${typeLabel} • by ${data.teacher ?? 'System'}</small>`;

list.prepend(item);

closeModal();

});
});
});

});

</script>

</body>
</html>

@endsection