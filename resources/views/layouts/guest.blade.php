<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — Fashion Corner</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'DM Sans', system-ui, sans-serif;
            background: #f7f7f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-brand {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #111;
        }
        .auth-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            max-width: 420px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container px-3">
        <div class="auth-card mx-auto p-4 p-md-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="auth-brand text-decoration-none">Fashion Corner</a>
                <p class="text-muted small mt-2 mb-0">Admin access only</p>
            </div>

            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
