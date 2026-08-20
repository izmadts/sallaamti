<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @font-face {
            font-family: 'NotoNastaliqUrdu';
            src: url('{{ storage_path(' fonts/NotoNastaliqUrdu-Regular.ttf') }}') format('truetype');
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: 'DejaVu Sans', sans-serif;
            background: #fdfaf3;
        }

        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            box-sizing: border-box;
        }

        .pattern-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ public_path(' images/islamic-pattern-bg.png') }}');
            background-repeat: repeat;
            opacity: 0.2;
            z-index: 0;
        }

        .border-frame {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 3px solid #b8962e;
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 14mm 20mm 8mm;
        }

        .logo {
            width: 100px;
            margin-bottom: 6px;
        }

        .kicker {
            font-size: 11px;
            letter-spacing: 3px;
            color: #b8962e;
            text-transform: uppercase;
        }

        .urdu-title {
            font-family: 'NotoNastaliqUrdu', 'DejaVu Sans', sans-serif;
            direction: rtl;
            unicode-bidi: bidi-override;
            font-size: 28px;
            color: #0d6b6b;
            margin: 8px 0 6px;
        }

        .english-title {
            font-size: 22px;
            color: #1a3c3c;
            font-weight: bold;
        }

        .divider {
            color: #b8962e;
            font-size: 16px;
            margin: 12px 0;
        }

        .presented-to {
            font-size: 12px;
            color: #777;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #1a3c3c;
            margin: 10px 0;
            border-bottom: 1px solid #d8c48a;
            display: inline-block;
            padding-bottom: 8px;
            min-width: 380px;
        }

        .course-line {
            font-size: 13px;
            color: #555;
            margin-top: 14px;
        }

        .course-title {
            font-size: 20px;
            font-weight: bold;
            color: #0d6b6b;
            margin: 6px 0 16px;
        }

        /* Footer laid out as a table row — reliable in PDF rendering */
        .footer-table {
            width: 100%;
            margin-top: 10mm;
        }

        .footer-table td {
            vertical-align: bottom;
            text-align: center;
            width: 33.33%;
        }

        .signature-img {
            height: 36px;
        }

        .signature-line {
            border-top: 1px solid #999;
            width: 150px;
            margin: 4px auto 0;
            padding-top: 4px;
            font-size: 11px;
            color: #555;
        }

        .seal {
            width: 100px;           
        }

        .qr img {
            width: 80px;
        }

        .qr-label {
            font-size: 7px;
            color: #999;
            margin-top: 2px;
        }

        .meta-footer {
            margin-top: 8mm;
            font-size: 9px;
            color: #999;
        }

        .cert-id {
            font-size: 9px;
            color: #b8962e;
            margin-top: 2px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="pattern-bg"></div>
        <div class="border-frame">
        </div>

        <div class="content">

            @if (file_exists(public_path('images/sallaamti-logo.png')))
            <img src="{{ public_path('images/sallaamti-logo.png') }}" class="logo"><br>
            @endif

            @if ($certificate->type === 'course')
            <div class="english-title">Certificate of Completion</div>
            @else
            <div class="english-title">{{ $certificate->title }}</div>
            @endif

            <div class="divider">❖ ─────────── ❖</div>

            <div class="presented-to">This certificate is proudly presented to</div>
            <div class="recipient-name">{{ $certificate->user->name }}</div>

            @if ($certificate->type === 'course')
            <div class="course-line">for successfully completing the course</div>
            <div class="course-title">{{ $certificate->course?->title }}</div>
            @endif

            <table class="footer-table">
                <tr>
                    <td>
                        @if (file_exists(public_path('images/signature-director.png')))
                        <img src="{{ public_path('images/signature-director.png') }}" class="signature-img"><br>
                        @endif
                        <div class="signature-line">Director, Sallaamti</div>
                    </td>
                    <td class="qr">
                        <img src="{{ $certificate->qrCodeBase64() }}">
                        <div class="qr-label">Scan to verify</div>
                    </td>
                    <td>
                        @if ($certificate->type === 'course')
                        @if (file_exists(public_path('images/signature-instructor.png')))
                        <img src="{{ public_path('images/signature-instructor.png') }}" class="signature-img"><br>
                        @endif
                        <div class="signature-line">Course Instructor</div>
                        @else
                        <div class="signature-line">Sallaamti Administration</div>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="meta-footer">
                Issued on {{ $certificate->issued_at->format('d F Y') }} &nbsp;|&nbsp; www.sallaamti.com
            </div>
            <div class="cert-id">Certificate ID: {{ $certificate->certificate_number }}</div>
            @if ($certificate->course?->track === 'skills')
            <div class="cert-id" style="color: #555; font-weight: normal;">In partnership with IZMA Digital Technology & Security</div>
            @endif

            @if (file_exists(public_path('images/gold-seal.png')))
            <img src="{{ public_path('images/gold-seal.png') }}" class="seal">
            @endif

        </div>
    </div>
</body>

</html>