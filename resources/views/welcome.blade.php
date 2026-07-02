{{-- Site name in <title> --}}
<title>{{ setting('site_name') }} — {{ setting('site_tagline') }}</title>

{{-- Social links --}}
<a href="{{ setting('social_facebook') }}">Facebook</a>
<a href="https://wa.me/{{ setting('social_whatsapp') }}">WhatsApp</a>

{{-- Contact section --}}
<p>{{ setting('site_email') }}</p>
<p>{{ setting('site_phone') }}</p>
<p>{{ setting('site_address') }}</p>