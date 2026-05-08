<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f4f6f9;
        }
        .error-container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { font-size: 100px; margin: 0; color: #e74c3c; }
        h2 { color: #333; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>{{ $message ?? 'The page you are looking for does not exist.' }}</p>
        <p>Requested URL: {{ $url ?? request()->url() }}</p>
        <a href="/">← Back to Home</a>
    </div>
</body>
</html>