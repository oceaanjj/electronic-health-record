<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a5e23;
            color: #e5e5e5ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            padding: 20px;
            border-radius: 8px;
        }

        h1 {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 0.5rem;
        }

        p {
            font-size: 1.25rem;
            margin-top: 0;
        }

        a {
            color: #ffe100ff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: #ffe100ff;

            text-decoration: underline;
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>403 Forbidden</h1>
        <p>You do not have permission to access this page.</p>
        <p>Please check your credentials or contact the administrator.</p>
        <a href="{{ route('home') }}">Return to Home Page</a>
    </div>
</body>

</html>