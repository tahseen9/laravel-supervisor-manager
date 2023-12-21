<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background-color: #f8fafc;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align:center;
        }
        p {
            color: #636b6f;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
    </style>
    <title>Laravel Supervisor Manager</title>
</head>
<body>
<div class="container">
    <h1>Welcome to Laravel Supervisor Manager</h1>

    <p><strong>Package Name:</strong> {{ $composerData['name'] }}</p>
    <p><strong>Description:</strong> {{ $composerData['description'] }}</p>
    <p><strong>Version:</strong> {{ $composerData['version'] }}</p>

    <!-- And so on for other information you want to display -->
</div>
</body>
</html>
