<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }
        .container {
            margin-top: 5rem;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>We'll Be Right Back!</h1>
        <p>{{ $message ?? 'We are performing some maintenance. Please check back soon.' }}</p>
        <p>Thank you for your patience.</p>
    </div>
</body>
</html>
