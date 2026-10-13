<x-app-layout>
    <!-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
            </div>
        </div>
    </div> -->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>Permissions</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Permissions Show</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check"></i><span class="alert-message ml-3">{{ \Session::get('success') }}</span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <!-- show start -->
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-30">
                    <div class="pd-20 card-box">
                        <div class="card">
                            <div class="card-header">
                                @can('role-create')
                                    <a class="btn btn-primary" href="{{ route('permissions.index') }}"><i class="icon-copy dw dw-back"></i> Permissions</a>
                                @endcan
                            </div>
                            <div class="card-body">
                                <div class="lead">
                                    <strong>Name:</strong>
                                    {{ $permission->name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- show end -->
                <div class="footer-wrap pd-20 mb-20 card-box">
                    Designed By <a href="#" target="_blank">EquPoint Platform</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>