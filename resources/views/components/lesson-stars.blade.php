{{--
    A row of stars (filled = completed lesson) as a kid-friendlier companion
    to the plain percentage bar — skipped for courses with a lot of lessons
    since a 30-star row stops being readable at a glance.
--}}
@props(['total' => 0, 'completed' => 0])

@if ($total > 0 && $total <= 12)
<div class="flex gap-0.5" title="{{ __('db.:completed of :total lessons complete', ['completed' => $completed, 'total' => $total]) }}">
    @for ($i = 1; $i <= $total; $i++)
    <span class="text-sm {{ $i <= $completed ? '' : 'opacity-25' }}">⭐</span>
    @endfor
</div>
@endif
