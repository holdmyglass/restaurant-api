<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: #2980b9;
            font-size: 20px;
            font-weight: 500;
        }

        p {
            margin-bottom: 20px;
        }

        .button {
            display: block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #c0392b;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .verification-code {
            font-weight: bold;
            font-size: 18px;
        }

        .url-container {
            background-color: #f2f2f2;
            padding: 10px;
            border-radius: 5px;
            word-break: break-all;
            margin-top: 10px;
        }

        .footer {
            margin-top: 20px;
            color: #777777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Hallo {{ $user->getUserDefaultProfile()->full_name }},</h1>

        <p>Welkom bij {{ $appName}}</p>

        <p>Uw account is succesvol geverifieerd.</p>

        <p>Vragen of zorgen? We zijn hier om te helpen via <a href="mailto:{{ $appSupportEmail }}">{{ $appSupportEmail }}</a></p>

        <p>Proost op jouw blogreis!</p>

        <p class="footer">Met vriendelijke groet,<br>Het {{ $appName }} Team 🚀</p>
    </div>
</body>

</html>
