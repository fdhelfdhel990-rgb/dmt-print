<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - DMT Print</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
</head>
<body class="login-body">
    <main class="login-shell">
        <section class="login-brand-panel">
            <a href="{{ route('home') }}" class="brand large">
                <span class="brand-mark">d</span>
                <span><b>DMT</b> Print<small>Darul Muttaqien Printing</small></span>
            </a>
            <div>
                <p class="eyebrow">Panel operasional</p>
                <h1>Kelola pesanan dengan lebih teratur.</h1>
                <p>Pantau pembayaran, produksi, stok, dan kas dalam satu tempat yang sederhana.</p>
            </div>
            <small>&copy; {{ date('Y') }} DMT Print</small>
        </section>

        <section class="login-form-panel">
            <form class="login-form" action="{{ route('admin.login.store') }}" method="POST">
                @csrf
                <p class="admin-eyebrow">Selamat datang</p>
                <h2>Masuk ke Panel Admin</h2>
                <p>Gunakan akun yang diberikan oleh Owner.</p>

                @if($errors->any())
                    <div class="login-alert" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="owner@dmtprint.com">
                </div>

                <div class="form-group">
                    <label for="password">Kata sandi</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••">
                </div>

                <label class="check-row">
                    <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
                </label>

                <button type="submit" class="button button-primary button-block">Masuk</button>
                <a href="{{ route('home') }}" class="back-link">← Kembali ke website</a>
            </form>
        </section>
    </main>
</body>
</html>
