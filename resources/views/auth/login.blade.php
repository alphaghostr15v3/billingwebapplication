<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 1.5rem;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .form-control {
            padding: 0.8rem 1.2rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
        }
        .btn-primary {
            background-color: #4f46e5;
            border: none;
            padding: 0.8rem;
            border-radius: 0.75rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">Welcome Back</h2>
            <p class="text-muted">Sign in to your account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-medium text-dark">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@example.com" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium text-dark">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div class="mb-4 d-flex justify-content-between">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label text-muted" for="remember">Remember me</label>
                </div>
                <a href="#" class="text-primary text-decoration-none small fw-medium">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
        </form>
    </div>
</body>
</html>
