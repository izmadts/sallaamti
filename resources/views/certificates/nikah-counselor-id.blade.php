<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    @php
    $application = \App\Models\MatchmakerApplication::where('user_id', $certificate->user_id)->where('status', 'certified')->first();
    $levelColors = [
        'nikah_counselor' => '#0d6b6b',
        'certified_nikah_counselor' => '#1a6fb8',
        'senior_nikah_counselor' => '#b8962e',
        'regional_nikah_coordinator' => '#7a2e8c',
    ];
    $accent = $levelColors[$application?->level ?? 'nikah_counselor'];
    $levelLabel = \App\Models\MatchmakerApplication::LEVELS[$application?->level ?? 'nikah_counselor'];
    @endphp
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
            border: 0.8px solid {{ $accent }};
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            padding: 3.6mm 5mm 2.6mm;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 1.4mm;
        }

        .logo {
            width: 16px;
            vertical-align: middle;
        }

        .brand {
            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #0d6b6b;
            vertical-align: middle;
            margin-left: 4px;
        }

        .kicker {
            font-size: 6px;
            letter-spacing: 1.5px;
            color: {{ $accent }};
            text-transform: uppercase;
            text-align: center;
        }

        .divider {
            width: 28mm;
            height: 0.6px;
            background: #d8c48a;
            margin: 1.4mm auto 1.8mm;
        }

        table.body-table {
            width: 100%;
            border-collapse: collapse;
        }

        .id-cell {
            vertical-align: middle;
        }

        .name {
            font-size: 11px;
            font-weight: bold;
            color: #1a3c3c;
            line-height: 1.2;
        }

        .role {
            font-size: 6.5px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: {{ $accent }};
            font-weight: bold;
            margin-top: 0.8mm;
        }

        .territory {
            font-size: 6.5px;
            color: #888;
            margin-top: 0.5mm;
        }

        .id-number {
            font-size: 7px;
            color: #0d6b6b;
            font-weight: bold;
            margin-top: 1.8mm;
        }

        .qr-cell {
            width: 13mm;
            text-align: center;
            vertical-align: middle;
        }

        .qr-cell img {
            width: 30px;
        }

        .qr-label {
            font-size: 4.8px;
            letter-spacing: 0.3px;
            color: #aaa;
            margin-top: 0.6mm;
        }

        .footer {
            text-align: center;
            font-size: 5.5px;
            color: #999;
            border-top: 0.5px solid #eee2c4;
            margin-top: 1.8mm;
            padding-top: 1.2mm;
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
            <div class="kicker">{{ __('db.Nikah Counselor Identity Card') }}</div>
            <div class="divider"></div>

            <table class="body-table">
                <tr>
                    <td class="id-cell">
                        <div class="name">{{ $certificate->user->name }}</div>
                        <div class="role">{{ $levelLabel }}</div>
                        @if ($application?->area)
                        <div class="territory">{{ $application->area }}</div>
                        @endif
                        <div class="id-number">{{ __('db.ID: :number', ['number' => $certificate->certificate_number]) }}</div>
                    </td>
                    <td class="qr-cell">
                        <img src="{{ $certificate->qrCodeBase64() }}">
                        <div class="qr-label">{{ __('db.SCAN TO VERIFY') }}</div>
                    </td>
                </tr>
            </table>

            <div class="footer">{{ __('db.Issued :date', ['date' => $certificate->issued_at->format('d M Y')]) }} &nbsp;·&nbsp; www.sallaamti.com</div>
        </div>
    </div>
</body>

</html>
