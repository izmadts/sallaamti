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

        .card {
            width: 85.6mm;
            height: 54mm;
            box-sizing: border-box;
            border: 2px solid #b8962e;
            position: relative;
            padding: 4mm 5mm;
        }

        .header {
            text-align: center;
            margin-bottom: 2mm;
        }

        .logo {
            width: 24px;
            vertical-align: middle;
        }

        .brand {
            font-size: 11px;
            font-weight: bold;
            color: #0d6b6b;
            vertical-align: middle;
            margin-left: 4px;
        }

        .kicker {
            font-size: 7px;
            letter-spacing: 2px;
            color: #b8962e;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 2mm;
        }

        table.body-table {
            width: 100%;
        }

        .name {
            font-size: 13px;
            font-weight: bold;
            color: #1a3c3c;
        }

        .role {
            font-size: 8px;
            color: #777;
            margin-top: 1mm;
        }

        .id-number {
            font-size: 8px;
            color: #0d6b6b;
            font-weight: bold;
            margin-top: 2mm;
        }

        .issued {
            font-size: 6.5px;
            color: #999;
            margin-top: 1mm;
        }

        .qr img {
            width: 44px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            @if (file_exists(public_path('images/sallaamti-logo.png')))
            <img src="{{ public_path('images/sallaamti-logo.png') }}" class="logo">
            @endif
            <span class="brand">SALLAAMTI</span>
        </div>
        <div class="kicker">Volunteer Identity Card</div>

        <table class="body-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="name">{{ $certificate->user->name }}</div>
                    <div class="role">Certified Volunteer</div>
                    <div class="id-number">ID: {{ $certificate->certificate_number }}</div>
                    <div class="issued">Issued {{ $certificate->issued_at->format('d M Y') }} &nbsp;|&nbsp; www.sallaamti.com</div>
                </td>
                <td class="qr" style="width: 48px; text-align: center; vertical-align: middle;">
                    <img src="{{ $certificate->qrCodeBase64() }}">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
