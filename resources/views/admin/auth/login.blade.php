<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — SR Chemical Industries Limited</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #0F5286 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 20px;
            background: #FFFFFF;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            border: none;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #F8FAFC 0%, #EFF6FF 100%);
            padding: 36px 30px 24px 30px;
            text-align: center;
            border-bottom: 1px solid #E2E8F0;
        }

        .brand-badge {
            background: #FFFFFF;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: inline-block;
            margin-bottom: 16px;
        }

        .brand-badge img {
            height: 40px;
            width: auto;
        }

        .login-body {
            padding: 36px 30px;
        }

        .form-control-lg {
            border-radius: 10px;
            font-size: 14px;
            padding: 12px 16px;
            border: 1px solid #CBD5E1;
        }

        .form-control-lg:focus {
            border-color: #0F5286;
            box-shadow: 0 0 0 3px rgba(15, 82, 134, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #0F5286 0%, #1D4ED8 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 6px 18px rgba(15, 82, 134, 0.35);
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 82, 134, 0.45);
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="brand-badge">
                <img src="{{ asset('assets/img/added/blue-logo.png') }}" alt="SR Chemical Logo">
            </div>
            <h2 class="h5 font-bold text-dark mb-1" style="font-family: 'Outfit', sans-serif;">ERP Administrative Portal</h2>
            <p class="text-13 text-muted mb-0">SR Chemical Industries Limited</p>
        </div>

        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger text-13 py-2 px-3 mb-3 rounded-3">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label font-semibold text-dark text-13">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control form-control-lg" required value="{{ old('email') }}" autofocus placeholder="admin@srchemicalindustries.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label font-semibold text-dark text-13">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control form-control-lg" required placeholder="••••••••">
                    </div>
                </div>

                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-13 text-muted" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('home') }}" class="text-13 text-primary text-decoration-none font-semibold">Back to Site</a>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Sign In to Dashboard <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
