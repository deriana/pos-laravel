<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code - Warung Sunda</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #F4F4F4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFF;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #7C4D2F; /* Warna khas Sunda, cokelat muda */
            color: #FFF;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .header h1 {
            font-size: 24px;
            margin: 0;
        }

        .otp-container {
            text-align: center;
            margin-top: 30px;
        }

        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #7C4D2F;
            padding: 10px 20px;
            background-color: #FFEB3B; /* Warna kuning hangat */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #888;
        }

        .footer p {
            font-size: 14px;
        }

        .footer a {
            color: #7C4D2F;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat datang di Warung Sunda!</h1>
        </div>

        <div class="otp-container">
            <p>Terima kasih telah bergabung dengan kami di Warung Sunda! Untuk melanjutkan, masukkan kode OTP berikut:</p>
            <div class="otp-code">
                {{ $otp }}
            </div>
            <p>Gunakan kode OTP ini untuk memverifikasi akun Anda. Kode ini berlaku selama 10 menit.</p>
        </div>

        <div class="footer">
            <p>Jika Anda tidak meminta verifikasi akun, abaikan email ini.</p>
            <p>Terima kasih telah memilih Warung Sunda. Nikmati cita rasa alam Sunda kami!</p>
            <p><a href="#">Kunjungi Warung Sunda</a></p>
        </div>
    </div>
</body>
</html>
