{{-- Renders one batch of cards — included for the first page load, and rendered
     standalone as the AJAX response body for every infinite-scroll batch after. --}}
@foreach ($paginated as $profile)
@include('nikah.partials.profile-card', ['profile' => $profile, 'sentInterestIds' => $sentInterestIds, 'savedProfileIds' => $savedProfileIds])
@endforeach
