<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Madekenya | Login</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="_token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/jpeg" href="{{ asset('assets/images/app-logo.jpeg') }}" />

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/line-awesome-1.3.0/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/authentication/ecargo-login.css') }}?v=1.1">
</head>
<body class="ecargo-login-body" data-base-url="{{ url('/') }}">

    <div id="load_screen">
        <div class="boxes">
            <div class="box"><div></div><div></div><div></div><div></div></div>
            <div class="box"><div></div><div></div><div></div><div></div></div>
            <div class="box"><div></div><div></div><div></div><div></div></div>
            <div class="box"><div></div><div></div><div></div><div></div></div>
        </div>
        <p class="neptune-loader-heading">Madekenya</p>
    </div>

    <div class="ecargo-login-page">
        <div class="ecargo-login-panel">
            <div class="ecargo-login-card">
                <div class="ecargo-brand">
                    <img src="{{ asset('assets/images/app-logo.jpeg') }}" alt="Madekenya Logo">
                    <h1>Madekenya</h1>
                    <p>Sign in to your cargo management account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="ecargo-alert-wrap">
                        @include('layout.alerts.message')
                    </div>

                    @error('email')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="ecargo-field">
                        <label for="email">Email or username</label>
                        <div class="ecargo-input-wrap">
                            <input id="email" type="text"
                                class="@error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}"
                                placeholder="Enter your email"
                                required autocomplete="email" autofocus>
                            <i class="las la-user-alt"></i>
                        </div>
                    </div>

                    <div class="ecargo-field">
                        <label for="password">Password</label>
                        <div class="ecargo-input-wrap ecargo-password-wrap">
                            <input id="password" type="password"
                                class="@error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Enter your password"
                                required autocomplete="current-password">
                            <i class="las la-lock"></i>
                            <button type="button" class="ecargo-password-toggle" id="toggle-password" aria-label="Show password" title="Show password">
                                <i class="las la-eye" id="toggle-password-icon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <label class="ecargo-remember" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>

                    <button class="ecargo-submit" type="submit">Sign In</button>
                </form>
            </div>
        </div>

        <aside class="ecargo-hero">
            <div class="ecargo-hero-content">
                <img class="ecargo-hero-logo" src="{{ asset('assets/images/app-logo.jpeg') }}" alt="Madekenya">

                <h2>Madekenya cargo operations, simplified.</h2>
                <p class="lead">Track parcels, manage fleets, and run your logistics business from one professional platform.</p>

                <ul class="ecargo-features">
                    <li><i class="las la-box"></i> Parcel &amp; customer management</li>
                    <li><i class="las la-truck"></i> Fleet, drivers &amp; deliveries</li>
                    <li><i class="las la-chart-line"></i> Real-time dashboards &amp; reports</li>
                </ul>

                <div class="ecargo-support">
                    <p style="margin: 0 0 0.5rem; opacity: 0.85;">Need help?</p>
                    <p style="margin: 0;">
                        <i class="lab la-whatsapp"></i>
                        <a href="https://wa.me/{{ config('app.support_whatsapp') }}" target="_blank" rel="noopener">{{ config('app.support_phone') }}</a>
                    </p>
                    <p style="margin: 0.5rem 0 0;">
                        <i class="las la-envelope"></i>
                        <a href="mailto:{{ config('app.support_email') }}">{{ config('app.support_email') }}</a>
                    </p>
                </div>
            </div>
        </aside>
    </div>

    <script src="{{ asset('assets/js/libs/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script>
        (function () {
            var input = document.getElementById('password');
            var btn = document.getElementById('toggle-password');
            var icon = document.getElementById('toggle-password-icon');
            if (!input || !btn || !icon) return;

            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.classList.toggle('la-eye', !show);
                icon.classList.toggle('la-eye-slash', show);
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                btn.setAttribute('title', show ? 'Hide password' : 'Show password');
            });
        })();
    </script>
</body>
</html>
