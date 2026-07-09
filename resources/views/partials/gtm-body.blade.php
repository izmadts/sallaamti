@if (setting('gtm_id'))
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ setting('gtm_id') }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif
@if (session('conversion_event'))
<script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push(@json(array_merge(['event' => session('conversion_event')], session('conversion_event_data', []))));
</script>
@endif
