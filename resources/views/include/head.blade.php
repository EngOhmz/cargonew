<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

<!-- CSRF Token -->
<meta name="_token" content="{{ csrf_token() }}">

<link rel="icon" type="image/jpeg" href="{{ asset('assets/images/app-logo.jpeg') }}" />

<!-- fonts library -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">

<script src="{{ asset('assets/js/app.js') }}"></script>

<link rel="stylesheet" href="{{ asset('assets/css/all.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/line-awesome-1.3.0/css/line-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">


<!-- Stack array for including inline css or head elements -->
@stack('plugin-styles')


