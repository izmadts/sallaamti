@foreach ($items as $item)
@continue (!$item)
@if ($item instanceof \App\Models\DuaRequest)
@include('dua-wall.partials.dua-card', ['dua' => $item])
@else
@include('dua-wall.partials.post-card', ['post' => $item])
@endif
@endforeach
