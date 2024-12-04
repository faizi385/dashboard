<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ReconEngine</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            /* background: linear-gradient(135deg, #6c757d, #343a40); */
            color: white;
        }

        a {
            color: inherit;
            text-decoration: inherit;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }

        .title {
            font-size: 4rem;
            font-weight: bold;
        }

        .description {
            font-size: 1.5rem;
            margin-top: 1rem;
        }

        .auth-links {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .auth-links a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background: #4c51bf;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .auth-links a:hover {
            background: #434190;
            transform: translateY(-2px);
        }

        .auth-links a:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .auth-links {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div class="title">ReconEngine</div>
            <div class="description">
                Welcome to ReconEngine, a B2B platform where Suppliers give exclusive Deals to Distributor. Experience seamless transactions and efficient reconciliation for your business.
            </div>
        </div>

        @if (Route::has('login'))
            <div class="auth-links">
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
</body>
</html>
