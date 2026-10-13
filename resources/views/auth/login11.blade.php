<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>@yield('title','') | Madekenya </title>
    <!-- initiate head with meta tags, css and script -->
    <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

<!-- CSRF Token -->
<meta name="_token" content="{{ csrf_token() }}">

<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.jpg') }}" />

<!-- fonts library -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">

<script src="{{ asset('assets/js/app.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/css/all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/line-awesome-1.3.0/css/line-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">


<!-- Stack array for including inline css or head elements -->
<link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/owl-carousel/owl.theme.default.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/authentication/auth_1.css') }}">



</head>
<body  data-base-url="{{url('/')}}">
    <!-- Loader Starts -->
    <div id="load_screen">
    <div class="boxes">
        <div class="box">
            <div></div><div></div><div></div><div></div>
        </div>
        <div class="box">
            <div></div><div></div><div></div><div></div>
        </div>
        <div class="box">
            <div></div><div></div><div></div><div></div>
        </div>
        <div class="box">
            <div></div><div></div><div></div><div></div>
        </div>
    </div>
    <p class="neptune-loader-heading">Madekenya</p>
</div>

    <!--  Loader Ends -->

    <!--  Main Container Starts  -->

    <!-- Main Body Starts -->
<div class="login-one">
    <div class="container-fluid login-one-container">
        <div class="p-30 h-100">
            <div class="row main-login-one h-100">
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 p-0">
                    <div class="login-one-start">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <h6 class="mt-2 text-primary text-center font-20">{{__('Log In')}}</h6>
                            <p class="text-center text-muted mt-3 mb-3 font-14">{{__('Please Log into your account')}}

                            @include('layout.alerts.message')
                            </p>
                            <div class="login-one-inputs mt-5">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <i class="las la-user-alt"></i>
                            </div>
                            <div class="login-one-inputs mt-3">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <i class="las la-lock"></i>
                            </div>
                            <div class="login-one-inputs check mt-4">
                                <input class="inp-cbx" id="cbx" type="checkbox" style="display: none">
                                <label class="cbx" for="cbx">
                                    <span>
                                        <svg width="12px" height="10px" viewBox="0 0 12 10">
                                            <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                        </svg>
                                    </span>
                                    <span class="font-13 text-muted">{{__('Remember me ?')}}</span>
                                </label>
                            </div>
                            <div class="login-one-inputs mt-4">
                                <button class="ripple-button ripple-button-primary btn-lg btn-login" type="submit">
                                    <div class="ripple-ripple js-ripple">
                                        <span class="ripple-ripple__circle"></span>
                                    </div>
                                    {{__('LOG IN')}}
                                </button>
                            </div>
                        </form>

                        
                        <div class="login-one-inputs social-logins mt-4">
                           
                            
                        </div> 
                    </div>
                </div>
                <div class="col-xl-8 col-lg-6 col-md-6 d-none d-md-block p-0">
                    <div class="slider-half">
                        <div class="slide-content">
                            <div class="top-sign-up ">
                                <div class="about-comp text-white mt-2">{{__('Madekenya')}}</div>
                                
                            </div>
                            <div class="clearfix"></div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Main Body Ends -->


    <!-- Main Container Ends -->
<script src="{{ asset('assets/js/all.js') }}"></script>
<script src="{{ asset('assets/js/toastr.min.js') }}"></script>
<script src="{{asset('assets/js/jautocalc.js')}}"></script>
<script src="{{asset('assets/js/select2.min.js')}}"></script>

<script>
    $(document).ready(function(){
   /*
                * Multiple drop down select
                */
$('.m-b').select2({ width: '100%', });
 

   
    });
   </script>



<script src="{{ asset('assets/js/loader.js') }}"></script>
<script src="{{ asset('assets/js/libs/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('plugins/owl-carousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('plugins/owl-carousel/owl.carousel.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/authentication/auth_1.js') }}"></script>)
</body>
</html>
