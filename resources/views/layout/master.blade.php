<!DOCTYPE html>
<html lang="en">

@include('layout.header')

<body>
    <!-- Main navbar -->
    @include('layout.main_navbar')
    <!-- /main navbar -->


    <!-- Page content -->
    <div class="page-content">
        <!-- Main sidebar -->
        @include('layout.aside2')
        <!-- /main sidebar -->



        <!-- Main content -->
        <div class="content-wrapper">
        @livewireScripts
            <!-- Inner content -->
            <div class="content-inner">

                <!-- Page header -->
                <div class="page-header page-header-light">
                  

                    <div class="breadcrumb-line breadcrumb-line-light header-elements-lg-inline">
                        <div class="d-flex">
                            <div class="breadcrumb">
                                <a href="{{url('home')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
                                <span class="breadcrumb-item active">Dashboard</span>
                            </div>

                            <a href="#" class="header-elements-toggle text-body d-lg-none"><i class="icon-more"></i></a>
                        </div>

                      
                    </div>
                </div>
                <!-- /page header -->

                @include('layout.alerts.message')
                <!-- Content area -->
                <div class="content">

                    @yield('content')

                </div>
                <!-- /content area -->


                <!-- Footer -->

                @include('layout.footer2')

                <!-- /footer -->

            </div>
            <!-- /inner content -->

        </div>
        <!-- /main content -->

    </div>

    
    <!-- /page content -->
    @include('layout.scripts')
    {!! Toastr::message() !!}
   
</body>


</html>

