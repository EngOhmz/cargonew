<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Madekenya | Register</title>
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
            <div class="ecargo-login-card ecargo-register-card">
                <div class="ecargo-brand">
                    <img src="{{ asset('assets/images/app-logo.jpeg') }}" alt="Madekenya Logo">
                    <h1>Madekenya</h1>
                    <p>Create your company account</p>
                    <span class="ecargo-trial-badge">
                        <i class="las la-gift"></i> 7-day free trial included
                    </span>
                </div>

                <form method="POST" action="{{ route('register_ema.post') }}">
                    @csrf

                    <div class="ecargo-alert-wrap">
                        @include('layout.alerts.message')
                    </div>

                    <p class="ecargo-note">
                        <i class="las la-building"></i>
                        A new company workspace will be created automatically for you with full superadmin access.
                    </p>

                    <div class="ecargo-field">
                        <label for="name">Company name</label>
                        <div class="ecargo-input-wrap">
                            <input id="name" type="text"
                                class="@error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}"
                                placeholder="Enter your company name"
                                required autocomplete="organization" autofocus>
                            <i class="las la-building"></i>
                        </div>
                        @error('name')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="ecargo-field">
                        <label for="email">Email address</label>
                        <div class="ecargo-input-wrap">
                            <input id="email" type="email"
                                class="@error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}"
                                placeholder="you@company.com"
                                required autocomplete="email">
                            <i class="las la-envelope"></i>
                        </div>
                        @error('email')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="ecargo-field">
                        <label for="password">Password</label>
                        <div class="ecargo-input-wrap">
                            <input id="password" type="password"
                                class="@error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Minimum 8 characters"
                                required autocomplete="new-password">
                            <i class="las la-lock"></i>
                        </div>
                        @error('password')
                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="ecargo-field">
                        <label for="password-confirm">Confirm password</label>
                        <div class="ecargo-input-wrap">
                            <input id="password-confirm" type="password"
                                name="password_confirmation"
                                placeholder="Re-enter your password"
                                required autocomplete="new-password">
                            <i class="las la-lock"></i>
                        </div>
                    </div>

                    <button class="ecargo-submit" type="submit">Create Account</button>
                </form>

                <p class="ecargo-footer-link">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>
            </div>
        </div>

        <aside class="ecargo-hero">
            <div class="ecargo-hero-content">
                <img class="ecargo-hero-logo" src="{{ asset('assets/images/app-logo.jpeg') }}" alt="Madekenya">

                <h2>Start managing cargo in minutes.</h2>
                <p class="lead">Register your company, invite your team, and take control of parcels, vehicles, and deliveries from day one.</p>

                <ul class="ecargo-features">
                    <li><i class="las la-rocket"></i> Quick setup - no technical skills needed</li>
                    <li><i class="las la-shield-alt"></i> Your own isolated company workspace</li>
                    <li><i class="las la-mobile-alt"></i> Web dashboard + mobile app access</li>
                </ul>

                <div class="ecargo-support">
                    <p style="margin: 0 0 0.5rem; opacity: 0.85;">Questions before signing up?</p>
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
</body>
</html>
