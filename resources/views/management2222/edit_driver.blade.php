@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}

<script src="{{asset('global_assets/js/plugins/forms/inputs/inputmask.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/uploaders/bs_custom_file_input.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/extensions/jquery_ui/core.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/tags/tagsinput.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/tags/tokenfield.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/inputs/touchspin.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/inputs/maxlength.min.js')}}"></script>
	<script src="{{asset('global_assets/js/plugins/forms/inputs/formatter.min.js')}}"></script>
    <script src="{{asset('global_assets/js/demo_pages/form_floating_labels.js')}}"></script>
@endpush
@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">

        <div class="col-12 col-sm-12 col-lg-12">

         <div class="card">
                <div class="card-header">
                    <h4>Kusahili Dereva</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link @if(empty($id)) active show @endif" href="#profile2"
                                    data-toggle="tab">Kuhariri Dereva</a>
                            </li>
                    </ul>

                    <div class="tab-content tab-bordered">


                    <div class="tab-pane fade @if(empty($id)) active show @endif" id="profile2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Hariri Dereva</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 ">
                                                
                                            {{ Form::model($driver, array('route' => array('driver.update', $driver->id),'role'=>'form','enctype'=>'multipart/form-data' ,'method' => 'PUT')) }}

                                               

                                                <div class="row">
                                                    <div
                                                        class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 offset-lg-3">
                                                        <div class="card">

                                                            <div class="card-body">

                                                                <div class="form-group row">
                                                                    <label class="control-label">Jina la Dereva<span class="required">
                                                                            </span></label> </label>
                                                                    <input type="text"  name="name" class="form-control" value="{{  old('name', $driver->name) }}" required>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label class="control-label">Namba ya Simu ya Dereva<span class="required">
                                                                            </span></label> </label>
                                                                    <input type="tel" name="phone"  class="form-control" value="{{  old('phone', $driver->phone) }}" >
                                                                </div>    


                                                                <div class="btn-bottom-toolbar text-right">
                                                                 
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-primary">Hariri</button>

                                                                        <button type="button" onclick="history.back()"
                                                                        class="btn btn-sm btn-danger">Ghairi</button>
                                                              
                                                                </div>
                                                               

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                
                                            
                                                {!! Form::close() !!}
                                            </div>
                                        </div>
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
    {!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/apex/apexcharts.min.js') !!}
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
    {!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
    @endpush
    
    @push('custom-scripts')
    
    @endpush