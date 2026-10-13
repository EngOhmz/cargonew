@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('assets/css/loader.css') !!}
    {!! Html::style('plugins/apex/apexcharts.css') !!}
    {!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    {!! Html::style('assets/css/elements/tooltip.css') !!}


    <style>
        #loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.75) url("{{ asset('assets/img/triangles_indicator.gif') }}") no-repeat center center;
            z-index: 99999;
        }
    </style>

    <script src="{{ asset('global_assets/js/plugins/forms/inputs/inputmask.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/uploaders/bs_custom_file_input.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/extensions/jquery_ui/core.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/tags/tagsinput.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/tags/tokenfield.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/inputs/touchspin.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/inputs/maxlength.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/plugins/forms/inputs/formatter.min.js') }}"></script>
    <script src="{{ asset('global_assets/js/demo_pages/form_floating_labels.js') }}"></script>
@endpush

@section('content')

    <!-- <div id="loading">
      <img id="loading-image" src=" {{ asset('public/assets/img/triangles_indicator.gif') }}" alt="Loading..." />
    </div> -->

    <div id='loader'></div>


    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-12">
                    <div class="card">
                    
                     <div class="form-group row">
                        <div class="col-lg-12" style="float: right;color:white;">
                            <livewire:modals.pacel-model />
                        </div>
                    </div>
                    
                    
                        <div class="card-header offset-5">
                            <h4>Utambulisho</h4>
                        </div>
                        <div class="card-body">

                            <p>
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible custom-success-box"
                                        style="margin: 15px;">
                                        <a href="{{ route('dashboard.index') }}" class="swal2-close" data-dismiss="alert"
                                            aria-label="close">&times;</a>
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach

                                    </div>
                                @endif
                            </p>



                            <div class="row form-prevent-multiple-submits">
                                <div class="mx-auto col-10 col-md-8 col-lg-6"">

                                    {{ Form::open(['route' => 'dashboard.store']) }}
                                    @method('POST')

                                    <div class="form-group row">

                                        <div class="col-lg-12">
                                            <input type="text" name="mteja"
                                                value="{{ isset($data) ? $data->name : '' }}" placeholder="Jina la mteja"
                                                required class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <input type="text" name="mpokeaji" required
                                                value="{{ isset($data) ? $data->name : '' }}"
                                                placeholder="Jina la mpokeaji" class="form-control">
                                        </div>
                                    </div>

                                    <livewire:get-pacel />

                                    <div class="form-group row">
                                        <div class="col-lg-offset-2 col-lg-12">

                                            <button class="btn btn-sm btn-primary float-right m-t-n-xs"
                                                type="submit">Save</button>

                                        </div>
                                    </div>
                                    {!! Form::close() !!}

                                    <div>
                                    </div>

                                   


                                    @if (isset($customer_ID))
                                        <div>
                                            <a class="btn btn-primary" href="{{ route('pacel_reg', $customer_ID) }}"
                                                target="_blank" role="button">Print Delivery Note</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('plugin-scripts')
    <script>
        $(function() {
            $("form").submit(function() {
                $('#loader').show();
            });
        });
    </script>


    {!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/apex/apexcharts.min.js') !!}
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
    {!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
@endpush
