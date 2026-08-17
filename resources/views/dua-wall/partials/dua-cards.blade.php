@foreach ($paginated as $dua)
@include('dua-wall.partials.dua-card', ['dua' => $dua])
@endforeach
