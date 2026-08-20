{{--
    Shared starter HTML templates for the "Advanced HTML" bulk-email compose
    mode — plain global JS so both the Users and Subscribers broadcast pages
    can @include this once instead of duplicating the markup. Inline CSS
    only (email clients don't reliably support <style> blocks or external
    stylesheets), matching the Sallaamti teal/gold palette.
--}}
<script>
    window.sallaamtiEmailTemplates = {
        simple: `<p>Assalamu Alaikum @{{name}},</p>
<p>[Write your update here...]</p>
<p>JazakAllah Khair,<br>Sallaamti Team</p>`,

        announcement: `<h2 style="color:#0d6b6b;margin:0 0 12px;">📢 Announcement Title</h2>
<p>Assalamu Alaikum @{{name}},</p>
<p>[Write your announcement here...]</p>
<div style="text-align:center;margin:24px 0;">
  <a href="https://sallaamti.com" style="background:#0d6b6b;color:#ffffff;padding:12px 28px;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block;">Learn More &rarr;</a>
</div>
<p>JazakAllah Khair,<br>Sallaamti Team</p>`,

        digest: `<h2 style="color:#0d6b6b;margin:0 0 4px;">🗞️ This Week at Sallaamti</h2>
<p>Assalamu Alaikum @{{name}},</p>
<div style="background:#fdfaf3;border-left:4px solid #b8962e;padding:14px 18px;margin:16px 0;border-radius:6px;">
  <p style="margin:0 0 6px;font-weight:bold;color:#b8962e;">✨ Highlight One</p>
  <p style="margin:0;">[Details here...]</p>
</div>
<div style="background:#fdfaf3;border-left:4px solid #0d6b6b;padding:14px 18px;margin:16px 0;border-radius:6px;">
  <p style="margin:0 0 6px;font-weight:bold;color:#0d6b6b;">📖 Highlight Two</p>
  <p style="margin:0;">[Details here...]</p>
</div>
<p>JazakAllah Khair,<br>Sallaamti Team</p>`,
    };
</script>
