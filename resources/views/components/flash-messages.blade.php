{{--
    Renders whatever flash message is on the session, in one consistent
    style, regardless of which controller/view set it. Included once in
    each layout so every page gets success/error/status/info handling
    without hand-rolling markup per view.
--}}
@if (session('success') || session('status') || session('error') || session('info') || session('warning'))
<div class="space-y-3 mb-6">
    @if (session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    @if (session('status'))
    <x-alert type="success">{{ session('status') }}</x-alert>
    @endif

    @if (session('error'))
    <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    @if (session('info'))
    <x-alert type="info">{{ session('info') }}</x-alert>
    @endif

    @if (session('warning'))
    <x-alert type="warning">{{ session('warning') }}</x-alert>
    @endif
</div>
@endif
