<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Tithandizane Women Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #7C3D5E;
            --primary-lt: #A85080;
            --accent: #E8976A;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #FDF6F0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px;
            border: 1px solid #EDE0D8;
            box-shadow: 0 12px 48px rgba(124,61,94,.12);
        }
        .brand-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, var(--primary-lt), var(--accent));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; color: #fff;
            margin: 0 auto 20px;
        }
        h2 {
            font-family: 'Playfair Display', serif;
            text-align: center;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .subtitle { text-align: center; font-size: .82rem; color: #9A7A8E; margin-bottom: 28px; }
        .form-label { font-size: .82rem; font-weight: 600; color: #6B3D57; }
        .form-control {
            border-color: #DDD0D8;
            border-radius: 10px;
            font-size: .9rem;
            padding: 11px 14px;
        }
        .form-control:focus {
            border-color: var(--primary-lt);
            box-shadow: 0 0 0 3px rgba(124,61,94,.1);
        }
        .input-group-text {
            background: #FAF2F7;
            border-color: #DDD0D8;
            border-radius: 10px 0 0 10px;
            color: var(--primary);
        }
        .input-group .form-control { border-radius: 0 10px 10px 0; }
        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-lt));
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: .95rem;
            width: 100%;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .privacy-note {
            text-align: center;
            font-size: .75rem;
            color: #B8A0B0;
            margin-top: 20px;
        }
        .invalid-feedback { font-size: .78rem; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <div class="brand-logo"><i class="bi bi-heart-fill"></i></div>
        <h2>Tithandizane</h2>
        <p class="subtitle">Women Hub · Admin Portal</p>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius:10px;font-size:.85rem;border:none;background:#FDECEC;color:#C0392B;" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="admin@tithandizane.mw" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <div class="mb-4 d-flex align-items-center gap-2">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" style="border-color:#DDD0D8;">
                <label class="form-check-label" for="remember" style="font-size:.82rem;color:#6B3D57;">Remember me</label>
            </div>
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <p class="privacy-note">
            <i class="bi bi-shield-lock me-1"></i>
            All data is encrypted and strictly confidential
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>