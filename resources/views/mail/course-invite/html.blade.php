<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Załóż konto w systemie e-learningowym</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
        }
        p {
            text-wrap: balance;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .footer {
            font-size: 12px;
            color: #777777;
            margin-top: 20px;
        }
        a {
            color: #0073aa;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0073aa;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #005f8a;
        }
        .header, .footer, .center {
            text-align: center
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{ asset('/mail.png') }}" alt="PROLIFE" style="max-width: 200px; height: auto">
    </div>
    <p style="margin-top: 0">Dzień Dobry,</p>
    <p>Wykładowca utworzył dla Ciebie zaproszenie do założenia konta w naszym systemie edukacyjnym.</p>
    <p>Aby utworzyć konto i rozpocząć szkolenie, kliknij poniższy link:</p>
    <p class="center">
        <a class="button" href="https://kursy.szkolenia-prolife.pl/zaproszenie/{{ $inviteCode }}?signature={{ $signature }}" target="_blank">Utwórz konto</a>
    </p>
    <p>Jeżeli nie spodziewałeś się tego e-maila, możesz go zignorować.</p>
    <p>Pozdrawiamy,<br>Zespół <strong>PROLIFE</strong></p>
    <div class="footer">
        <p>Wiadomość została wygenerowana automatycznie. Prosimy na nią nie odpowiadać.</p>
    </div>
</div>
</body>
</html>
