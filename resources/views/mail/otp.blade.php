<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP - Workshop Keguruan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #2e86de;
            --bg-light: #f9f9f9;
            --text-dark: #333;
            --text-muted: #777;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 520px;
            margin: auto;
            background: white;
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: var(--primary);
            margin-bottom: 6px;
            font-size: 26px;
        }

        .header span {
            font-size: 15px;
            color: var(--text-muted);
        }

        .greeting {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .message {
            font-size: 15px;
            color: var(--text-dark);
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 10px;
            text-align: center;
            color: var(--primary);
            margin: 30px 0;
        }

        .note {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 25px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #aaa;
            margin-top: 40px;
        }

        @media (max-width: 600px) {
            .otp-code {
                font-size: 28px;
                letter-spacing: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Workshop Keguruan</h1>
            <span>Verifikasi Akun Anda</span>
        </div>

        <div class="greeting">
            Halo{{ isset($user) ? ' ' . $user->nama : '' }},
        </div>

        <div class="message">
            Terima kasih telah mendaftar di <strong>Workshop Keguruan</strong>.<br>
            Berikut adalah kode OTP Anda untuk verifikasi akun:
        </div>

        <div class="otp-code">
            {{ $otp }}
        </div>

        <div class="message">
            Kode ini hanya berlaku selama <strong>10 menit</strong>.<br>
            Jangan bagikan kode ini kepada siapa pun demi keamanan akun Anda.
        </div>

        <div class="note">
            Jika Anda tidak meminta kode ini, abaikan email ini.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Workshop Keguruan. Semua hak dilindungi.
        </div>
    </div>

    {{-- Hanya untuk debugging lokal --}}
    <script>
        console.log("OTP Anda adalah: {{ $otp }}");
    </script>
</body>
</html>
