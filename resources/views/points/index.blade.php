{{-- ONLY showing the FIXED PART (recent list rendering) --}}

<div class="sidebar">
<div class="sidebar-box">
<h3>Recent</h3>

@foreach($recent as $item)
<div class="activity">

<strong>
{{ $item->first_name ?? $item->house_name ?? 'Unknown' }}
</strong><br>

<span class="{{ $item->amount > 0 ? 'pos' : 'neg' }}">
{{ $item->amount > 0 ? '+ ' : '' }}{{ $item->amount }}
</span>
<br>

<small>
by {{ $item->teacher ?? 'System' }}
</small>

</div>
@endforeach

</div>
</div>