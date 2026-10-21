@props(['phone'])

{{-- A phone number shown anywhere in the Nikah admin views — the member's
     own or their guardian's — as a one-tap WhatsApp link plus a copy icon,
     so an admin/counselor can reach out or paste the number elsewhere
     without retyping it. Reuses the same whatsapp_link()/normalize_
     whatsapp_number() helpers the member-facing "Contact Support on
     WhatsApp" links already go through, so the digit-normalization (local
     0XXX vs +92, spaces/dashes) stays in one place. --}}
@if ($phone)
<span class="inline-flex items-center gap-1.5">
    <a href="{{ whatsapp_link(null, $phone) }}" target="_blank" rel="noopener"
        class="inline-flex items-center gap-1 hover:text-green-600 transition" title="{{ __('db.Open in WhatsApp') }}">
        <span>💬</span>
        <span>{{ $phone }}</span>
    </a>
    <x-copy-button :value="$phone" />
</span>
@else
<span class="text-gray-400">—</span>
@endif
