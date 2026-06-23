<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Product System</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; }
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #0f172a, #0f766e);
            color: #111827;
        }
        .card {
            width: min(100% - 32px, 430px);
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 30px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 8px;
            background: #0f766e;
            color: #fff;
            font-weight: 800;
        }
        h1 { margin: 0; font-size: 28px; }
        p { margin: 6px 0 0; color: #6b7280; }
        label { display: block; margin-bottom: 7px; font-weight: 700; }
        .field { margin-bottom: 16px; }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            min-height: 44px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 10px 12px;
        }
        input:focus {
            border-color: #0f766e;
            outline: 3px solid rgba(15, 118, 110, .18);
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            color: #4b5563;
            font-size: 14px;
        }
        .remember label { margin: 0; font-weight: 600; }
        .button {
            width: 100%;
            min-height: 44px;
            border: 0;
            border-radius: 6px;
            background: #0f766e;
            color: #fff;
            cursor: pointer;
            font-weight: 800;
        }
        .button:hover { background: #115e59; }
        .errors {
            margin-bottom: 18px;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 12px 14px;
            background: #fef2f2;
            color: #991b1b;
        }
        .error-text {
            margin-top: 6px;
            color: #b91c1c;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">
            <div class="mark">PS</div>
            <div>
                <h1>Admin Login</h1>
                <p>Sign in to manage products and categories.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="errors">
                <strong>Please check your login details.</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember">
                <input id="remember" type="checkbox" name="remember">
                <label for="remember">Remember me</label>
            </div>

            <button class="button" type="submit">Log in</button>
        </form>
    </main>
</body>
</html>
