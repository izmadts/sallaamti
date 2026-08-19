<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 85.6mm;
            height: 54mm;
            font-family: 'DejaVu Sans', sans-serif;
            background: #fdfaf3;
        }

        /* Full-bleed page: no border/padding on this element itself, so its
           box-sizing can never fight its fixed width/height — the frame and
           the safe-area padding both live on separate inner layers instead. */
        .page {
            width: 85.6mm;
            height: 54mm;
            position: relative;
            box-sizing: border-box;
        }

        .border-frame {
            position: absolute;
            top: 2.2mm;
            left: 2.2mm;
            right: 2.2mm;
            bottom: 2.2mm;
            border: 0.8px solid #b8962e;
            z-index: 1;
        }

        /* No fixed width/height here — a plain block naturally fills the
           page and lets padding carve out the safe margin, so nothing can
           be pushed past the printable edge. */
        .content {
            position: relative;
            z-index: 2;
            padding: 4.2mm 5mm 3mm;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 1.8mm;
        }

        .logo {
            width: 18px;
            vertical-align: middle;
        }

        .brand {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #0d6b6b;
            vertical-align: middle;
            margin-left: 4px;
        }

        .kicker {
            font-size: 6.5px;
            letter-spacing: 2px;
            color: #b8962e;
            text-transform: uppercase;
            text-align: center;
        }

        .divider {
            width: 28mm;
            height: 0.6px;
            background: #d8c48a;
            margin: 1.8mm auto 2.2mm;
        }

        table.body-table {
            width: 100%;
            border-collapse: collapse;
        }

        .id-cell {
            vertical-align: middle;
        }

        .name {
            font-size: 12px;
            font-weight: bold;
            color: #1a3c3c;
            line-height: 1.25;
        }

        .role {
            font-size: 7px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #888;
            margin-top: 0.8mm;
        }

        .id-number {
            font-size: 7.5px;
            color: #0d6b6b;
            font-weight: bold;
            margin-top: 2.2mm;
        }

        .qr-cell {
            width: 13mm;
            text-align: center;
            vertical-align: middle;
        }

        .qr-cell img {
            width: 32px;
        }

        .qr-label {
            font-size: 5px;
            letter-spacing: 0.3px;
            color: #aaa;
            margin-top: 0.6mm;
        }

        .footer {
            text-align: center;
            font-size: 5.8px;
            color: #999;
            border-top: 0.5px solid #eee2c4;
            margin-top: 2.5mm;
            padding-top: 1.5mm;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="border-frame"></div>

        <div class="content">
            <div class="header">
                @if (file_exists(public_path('images/sallaamti-logo.png')))
                <img src="{{ public_path('images/sallaamti-logo.png') }}" class="logo">
                @endif
                <span class="brand">SALLAAMTI</span>
            </div>
            <div class="kicker">Volunteer Identity Card</div>
            <div class="divider"></div>

            <table class="body-table">
                <tr>
                    <td class="id-cell">
                        <div class="name">{{ $certificate->user->name }}</div>
                        <div class="role">Certified Volunteer</div>
                        <div class="id-number">ID: {{ $certificate->certificate_number }}</div>
                    </td>
                    <td class="qr-cell">
                        <img src="{{ $certificate->qrCodeBase64() }}">
                        <div class="qr-label">SCAN TO VERIFY</div>
                    </td>
                </tr>
            </table>

            <div class="footer">Issued {{ $certificate->issued_at->format('d M Y') }} &nbsp;·&nbsp; www.sallaamti.com</div>
        </div>
    </div>
</body>

</html>
