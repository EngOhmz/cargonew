<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



	<title>@yield('title','') | Madekenya</title>
    
    @livewireStyles
	<!-- Global stylesheets -->
	<link rel="icon" type="image/jpeg" href="{{ asset('assets/images/app-logo.jpeg') }}" />
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{asset('global_assets/css/icons/icomoon/styles.min.css')}}" rel="stylesheet" type="text/css">
	<link href="{{asset('assets2/css/all.min.css')}}" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->
	<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
    @stack('plugin-styles')
</head>
