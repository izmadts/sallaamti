<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? config('app.name') }}</title>
</head>

<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;">

                    <tr>
                        <td align="center" style="background:#0f766e;padding:25px;">
                            <img src="{{ asset('img/logo-w.png') }}" alt="{{ __('db.Sallaamti') }}" width="120">

                            <h2 style="color:#fff;margin:15px 0 0;">
                                {{ __('db.Sallaamti') }}
                            </h2>

                            <p style="color:#d1fae5;margin:5px 0;">
                                {{ __('db.Spreading Peace Through Quran & Sunnah') }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:40px;">

                            @yield('content')

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8fafc;padding:25px;text-align:center;">

                            <p style="margin:0;font-size:14px;color:#555;">
                                {{ __('db.Need Help?') }}
                            </p>

                            <p style="margin:10px 0;">
                                📞 +92 334 6145566
                            </p>

                            <p style="margin:0;">
                                🌐 www.sallaamti.com
                            </p>

                            <hr style="margin:20px 0;">

                            <p style="font-size:12px;color:#888;">
                                {{ __('db.© :year Sallaamti. All Rights Reserved.', ['year' => date('Y')]) }}
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>