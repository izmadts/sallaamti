@if (setting('gtm_id'))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ setting('gtm_id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
@if (session('conversion_event'))
@php
    // Assigned to a plain PHP var before @json() — Blade's built-in @json
    // directive splits its raw argument text on EVERY top-level comma to
    // pull out optional $options/$depth params, with zero awareness of
    // nested brackets. This array literal happens to have exactly the 2
    // commas @json expects, so it reconstructs correctly today by
    // coincidence — but breaks silently the moment either array gains
    // another comma-separated entry. A bare variable has no comma, so
    // it's safe regardless of what the array itself contains.
    $conversionEventData = array_merge(['event' => session('conversion_event')], session('conversion_event_data', []));
@endphp
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push(@json($conversionEventData));
</script>
@endif
