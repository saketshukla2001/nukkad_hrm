<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Nukkad HRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="login-page">

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-brand">
                <div class="login-logo">NH</div>
                <h1>Nukkad HRM</h1>
                <p>Sign in to your admin panel</p>
            </div>

            @if(session('error'))
                <div class="error-box">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="admin@nukkad.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-login">Sign In →</button>
            </form>
        </div>
    </div>

</body>
</html>
