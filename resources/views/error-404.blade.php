<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Neptune Error 404 2 | Neptune - Multipurpose Bootstrap Admin Template </title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
    <!-- Common Styles Starts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Common Styles Ends -->
    <!-- Common Icon Starts -->
    <link rel="stylesheet" href="{{ asset('assets/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/line-awesome-1.3.0/css/line-awesome.min.css') }}">
    <!-- Common Icon Ends -->
    <!-- Page Level Plugin/Style Starts -->
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/error/error_2.css') }}">
    <!-- Page Level Plugin/Style Ends -->
</head>
<body>
<!-- Loader Starts -->
@include('include.loader')
<!--  Loader Ends -->
<!-- Main Body Starts -->
<div class="error-wrapper">
    <div class="col-md-6 error-left-section">
        <a class="navbar-brand" href="index-2.html">NEPTUNE</a>
        <div class="error-left-content">
            <h1 class="mb-3 text-white">{{__('Oops!')}}</h1>
            <p class="font-25 mb-5 text-white">{{__('Sorry, We can\'t seem to find the page you\'re looking for.')}}</p>
            <a class="btn bg-gradient-primary text-white" href="{{ url('/') }}">{{__('Back to Home')}}</a>
        </div>
    </div>
    <div class="col-md-6 error-right-section">
        <div class="error-menu">
           
            <a class="nav-link" target="_blank" href="{{ url('/pages/contact-us') }}">{{__('Contact Us')}}</a>
        </div>
        <img src="{{ url('assets/img/error-404-1.jpg') }}" class="error-img" />
    </div>
</div>
<!-- Main Body Ends -->
<!-- Footer Starts -->
<div class="faq-footer">
    <div class="d-flex align-items-center justify-content-between">
        <p class="">{{__('Copyright © 2021')}} <a target="_blank" href="http://neptuneweb.xyz">NeptuneWeb</a> {{__('| All rights reserved.')}}</p>
        <p class="">{{__('Crafted with extra')}} <i class="las la-heart text-danger"></i></p>
    </div>
</div>
<!-- Footer Ends -->
<!-- Arrow Starts -->
<div class="arrow" style="display: none;">
    <i class="las la-angle-up"></i>
</div>
    <!-- Arrow Ends -->
    <!-- Common Script Starts -->
    <script src="{{ asset('assets/js/all.js') }}"></script>
    <!-- Common Script Ends -->
    <!-- Page Level Plugin/Script Starts -->
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('assets/js/pages/faq/faq.js') }}"></script>
    <!-- Page Level Plugin/Script Ends -->
</body>
</html>
