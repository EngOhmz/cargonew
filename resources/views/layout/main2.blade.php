<!DOCTYPE html>
<html lang="en">


<!-- auth-register.html  21 Nov 2019 04:05:01 GMT -->
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <?php
$settings= App\Models\System::first();
?>
  <title>{{ !empty($settings->name) ? $settings->name: ''}}</title>
  <!-- General CSS Files -->
  <link rel='shortcut icon' type='image/x-icon' href="{{url('public/assets/img/logo')}}/{{$settings->picture}}" />
  <link rel="stylesheet" href="{{url('assets/css/app.min.css')}}">
  <link rel="stylesheet" href="{{url('assets/bundles/jquery-selectric/selectric.css')}}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{url('assets/css/style.css')}}">
  <link rel="stylesheet" href="{{url('assets/css/components.css')}}">
  <!-- Custom style CSS -->
  <link rel="stylesheet" href="{{url('assets/css/custom.css')}}">

</head>

<body>
  <div class="loader"></div>
  <div id="app">
  @yield('contents')
  </div>
  <!-- General JS Scripts -->
  <script src="{{url('assets/js/app.min.js')}}"></script>
  <!-- JS Libraies -->
  <script src="{{url('assets/bundles/jquery-pwstrength/jquery.pwstrength.min.js')}}"></script>
  <script src="{{url('assets/bundles/jquery-selectric/jquery.selectric.min.js')}}"></script>
  <!-- Page Specific JS File -->
  <script src="{{url('assets/js/page/auth-register.js')}}"></script>
  <!-- Template JS File -->
  <script src="{{url('assets/js/scripts.js')}}"></script>
  <!-- Custom JS File -->
  <script src="{{url('assets/js/custom.js')}}"></script>
</body>


<!-- auth-register.html  21 Nov 2019 04:05:02 GMT -->
</html>