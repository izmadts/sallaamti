<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
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
            background: #ffffff;
        }

        /* Full-bleed page: border and safe-area padding live on separate
           inner layers (same DomPDF-safe pattern as the other certificate
           templates) rather than combined width+height+border+padding on
           one element. */
        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            box-sizing: border-box;
        }

        .border-frame {
            position: absolute;
            top: 10mm;
            left: 10mm;
            right: 10mm;
            bottom: 10mm;
            border: 2px solid #cfd4db;
            z-index: 1;
        }

        .border-frame-inner {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 0.5px solid #e5e8ec;
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 18mm 20mm 8mm;
        }

        .logo {
            width: 90px;
            margin-bottom: 4px;
        }

        .partner-badge {
            display: inline-block;
            margin: 8px 0 4px;
            padding: 4px 16px;
            background: #f3f5f7;
            border: 1px solid #cfd4db;
            border-radius: 20px;
            font-size: 10px;
            letter-spacing: 1.5px;
            color: #5b6b7f;
            text-transform: uppercase;
        }

        .english-title {
            font-size: 24px;
            color: #1a2332;
            font-weight: bold;
            margin-top: 10px;
        }

        .divider {
            width: 60px;
            height: 2px;
            background: #cfd4db;
            margin: 12px auto;
        }

        .presented-to {
            font-size: 11px;
            color: #8a94a3;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .recipient-name {
            font-size: 30px;
            font-weight: bold;
            color: #1a2332;
            margin: 10px 0;
            border-bottom: 1px solid #cfd4db;
            display: inline-block;
            padding-bottom: 8px;
            min-width: 380px;
        }

        .course-line {
            font-size: 12px;
            color: #6b7688;
            margin-top: 14px;
        }

        .course-title {
            font-size: 19px;
            font-weight: bold;
            color: #3d4a5c;
            margin: 6px 0 16px;
        }

        .footer-table {
            width: 100%;
            margin-top: 8mm;
        }

        .footer-table td {
            vertical-align: bottom;
            text-align: center;
            width: 33.33%;
        }

        .signature-img {
            height: 34px;
        }

        .signature-line {
            border-top: 1px solid #cfd4db;
            width: 150px;
            margin: 4px auto 0;
            padding-top: 4px;
            font-size: 11px;
            color: #6b7688;
        }

        .qr img {
            width: 76px;
        }

        .qr-label {
            font-size: 7px;
            color: #a3abb8;
            margin-top: 2px;
        }

        .meta-footer {
            margin-top: 8mm;
            font-size: 9px;
            color: #a3abb8;
        }

        .cert-id {
            font-size: 9px;
            color: #6b7688;
            margin-top: 2px;
            font-weight: bold;
        }

        .izma-footer {
            font-size: 8px;
            color: #8a94a3;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="border-frame"></div>
        <div class="border-frame-inner"></div>

        <div class="content">

            @if (file_exists(public_path('images/sallaamti-logo.png')))
            <img src="{{ public_path('images/sallaamti-logo.png') }}" class="logo"><br>
            @endif

            <div class="partner-badge">Presented by IZMA Digital Technology &amp; Security</div>

            <div class="english-title">
                @if ($certificate->type === 'course')
                Certificate of Completion
                @else
                {{ $certificate->title }}
                @endif
            </div>

            <div class="divider"></div>

            <div class="presented-to">This certificate is proudly presented to</div>
            <div class="recipient-name">{{ $certificate->user->name }}</div>

            @if ($certificate->type === 'course')
            <div class="course-line">for successfully completing the digital skills course</div>
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
                        <div class="qr-label">SCAN TO VERIFY</div>
                    </td>
                    <td>
                        <div class="signature-line">Sallaamti &amp; IZMA Digital Technology &amp; Security</div>
                    </td>
                </tr>
            </table>

            <div class="meta-footer">
                Issued on {{ $certificate->issued_at->format('d F Y') }} &nbsp;|&nbsp; www.sallaamti.com
            </div>
            <div class="cert-id">Certificate ID: {{ $certificate->certificate_number }}</div>
            <div class="izma-footer">In partnership with IZMA Digital Technology &amp; Security &nbsp;|&nbsp; izmadts.com</div>

        </div>
    </div>
</body>

</html>
