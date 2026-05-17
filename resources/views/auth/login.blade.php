<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sleman Akses Admin - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f8fafc;
        }

        .login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
        }

        .login-visual {
            position: relative;
            background: radial-gradient(circle at top, rgba(15, 118, 110, 0.4), transparent 60%),
                radial-gradient(circle at 30% 60%, rgba(34, 197, 94, 0.35), transparent 55%),
                linear-gradient(135deg, #0f172a, #0f766e);
            overflow: hidden;
        }

        .login-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: linear-gradient(0deg, rgba(255, 255, 255, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: 0.35;
        }

        .login-visual-content {
            position: relative;
            z-index: 1;
            padding: 64px;
            color: #ffffff;
        }

        .login-card {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
            padding: 32px;
        }

        .login-title {
            font-weight: 800;
        }

        .login-subtitle {
            color: #64748b;
        }

        @media (max-width: 992px) {
            .login-page {
                grid-template-columns: 1fr;
            }

            .login-visual {
                min-height: 220px;
            }
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="login-visual">
            <div class="login-visual-content">
                <h1 class="display-6 fw-bold">Sleman Akses</h1>
                <p class="fs-5">Dashboard pengelolaan laporan aksesibilitas.</p>
                <div class="mt-4">
                    <span class="badge text-bg-light">Admin Only</span>
                </div>
            </div>
        </div>

        <div class="login-card">
            <div class="login-box">
                <div class="mb-4">
                    <h2 class="login-title">Sleman Akses Admin</h2>
                    <p class="login-subtitle">Masuk untuk mengelola laporan dan peta.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required
                            autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
