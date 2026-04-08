@extends('layouts.app')

@section('content')

<style>
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
    background:linear-gradient(145deg,var(--colour) 0%,rgba(0,0,0,0.4) 100%);
    border-radius:16px;
    padding:20px;
    text-align:center;
    cursor:pointer;
}

.house-emoji { font-size:64px; margin-bottom:10px; }
.house-name { font-weight:800; color:white; }

.card {
    background:#f3f4f6;
    padding:18px;
    border-radius:10px;
    margin-bottom:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.left { display:flex; align-items:center; gap:16px; }
.crest { font-size:32px; }

.name { font-weight:800; font-size:20px; }
.meta { font-size:12px; color:#555; }

.actions { display:flex; gap:8px; }

.btn { border:none; border-radius:10px; cursor:pointer; font-weight:800; }
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
</style>

<div class="layout">

<div class="main">

<h1>Award Points</h1>

<!-- HOUSES -->
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
@else 🦡
@endif
</div>
<div class="house-name">{{ $house->name }}</div>
</div>

</form>
@endforeach

</div>
</div>

<!-- STUDENTS -->
@foreach($students as $student)
<div class="card">

<div class="left">
<div class="crest">
@if($student->house_name == 'Gryffindor') 🦁
@elseif($student->house_name == 'Slytherin') 🐍
@elseif($student->house_name == 'Ravenclaw') 🦅
@else 🦡
@endif
</div>

<div>
<div class="name">
<a href="{{ route('students.show', $student->id) }}">
{{ $student->first_name }} {{ $student->last_name }}
</a>
</div>

<div class="meta">
{{ $student->house_name }} • 
<span class="points">{{ $student->house_points }}</span> pts
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

<!-- RECENT -->
<div class="sidebar">
<div class="sidebar-box">
<h3>Recent</h3>

<div id="recentList">

@foreach($recent as $r)
<div class="activity">
<strong>
{{ $r->first_name ? $r->first_name . ' ' . $r->last_name : $r->house_name }}
</strong><br>

<span class="{{ $r->amount > 0 ? 'pos' : 'neg' }}">
{{ $r->amount > 0 ? '+' : '' }}{{ $r->amount }}
</span>

<br>
<small>{{ ucfirst($r->category) }} • {{ $r->teacher ?? 'System' }}</small>
</div>
@endforeach

</div>

</div>
</div>

</div>

<div id="toast"></div>

<script>
document.querySelectorAll('form.ajax').forEach(form=>{
form.addEventListener('submit',function(e){
e.preventDefault();

if (this.dataset.loading === "1") return;
this.dataset.loading = "1";

let fd=new FormData(this);

fetch('/points',{
method:'POST',
headers:{
'X-CSRF-TOKEN':document.querySelector('input[name="_token"]').value,
'Accept':'application/json'
},
body:fd
})
.then(r=>r.json())
.then(data=>{
let amt=parseInt(data.amount||0);

// toast
let t=document.getElementById('toast');
t.className='show '+(amt>0?'toast-success':'toast-error');
t.innerText=(amt>0?'+':'')+amt;
setTimeout(()=>t.className='',1000);

// update points
let card=this.closest('.card');
if(card){
let el=card.querySelector('.points');
el.innerText=parseInt(el.innerText)+amt;
}

// recent
let list=document.getElementById('recentList');
let item=document.createElement('div');
item.className='activity';

let label = data.student ? data.student : data.house;

item.innerHTML=`
<strong>${label}</strong><br>
<span class="${amt>0?'pos':'neg'}">${amt>0?'+':''}${amt}</span>
<br>
<small>${data.category ?? 'update'} • ${data.teacher ?? 'System'}</small>
`;

list.prepend(item);

this.dataset.loading = "0";
})
.catch(()=>{
let t=document.getElementById('toast');
t.className='show toast-error';
t.innerText='Error saving';
setTimeout(()=>t.className='',1500);

this.dataset.loading = "0";
});
});
});
</script>

@endsection