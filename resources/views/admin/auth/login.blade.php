<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Nukkad HRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    * {
        box-sizing: border-box; /* ✅ IMPORTANT FIX */
    }

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        height: 100vh;
        background: linear-gradient(135deg, #4f46e5, #06b6d4);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-card {
        background: #fff;
        padding: 30px 25px;
        width: 100%;
        max-width: 380px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 10px;
    }

    .login-card p.subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 25px;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group input {
        width: 100%;
        padding: 12px 14px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-size: 14px;
        outline: none;
    }

    .form-group input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79,70,229,0.1);
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 6px;
        background: #4f46e5;
        color: #fff;
        font-size: 15px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-login:hover {
        background: #4338ca;
    }

    .error-box {
        background: #fee2e2;
        color: #991b1b;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
        text-align: center;
    }

    .brand {
        text-align: center;
        font-weight: bold;
        color: #4f46e5;
        margin-bottom: 5px;
        font-size: 18px;
    }
</style>
</head>
<body>

    <div class="login-card">
        <div class="brand">Nukkad HRM</div>
        <h2>Admin Login</h2>
        <p class="subtitle">Sign in to your admin panel</p>

        @if(session('error'))
            <div class="error-box">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="form-group">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

</body>
</html>
