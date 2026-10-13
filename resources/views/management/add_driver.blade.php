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
                    <h4>Kusajili Madereva</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link @if(empty($id)) active show @endif" href="#home2"
                                    data-toggle="tab">Orodha za Madereva (Driver List)</a>
                            </li>
                            <li class="nav-item"><a class="nav-link @if(!empty($id)) active show @endif"
                                    href="#profile2" data-toggle="tab">Dereva Mpya (New Driver)</a>
                            </li>
                    </ul>

                    <div class="tab-content tab-bordered">
                            <!-- ************** general *************-->
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2">

                                <div class="table-responsive">
                                    <table class="table table-striped " id="table-1">
                                        <thead>
                                            <tr>
                                                <th >#</th>
                                                <th>Driver ID</th>
                                                <th>Jina la Dereva</th>
                                                <th>Barua Pepe (Email)</th>
                                                <th>Namba ya Simu</th>
                                                <th>Status</th>
                                                <th>Assigned Date</th>
                                                <th>Car Name</th>

                                                <th class="col-sm-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        @if(!@empty($drivers))
                                              @foreach ($drivers as $row)
                                                <tr class="gradeA even" role="row">
                                                  <th>{{ $loop->iteration }}</th>
                                                   <td><span class="badge badge-info">{{$row->driver_id}}</span></td>
                                                   <td>{{$row->name}}</td>
                                                   <td>{{$row->email ?? 'N/A'}}</td>
                                                   <td>{{$row->phone}}</td>
                                                   <td>
                                                       @if($row->status == 1)
                                                           <span class="badge badge-success">Active (Available)</span>
                                                       @elseif($row->status == 2)
                                                           <span class="badge badge-warning">Assigned</span>
                                                       @else
                                                           <span class="badge badge-secondary">Unknown</span>
                                                       @endif
                                                   </td>
                                                   <td>{{$row->assigned_date ?? 'N/A'}}</td>
                                                   <td>
                                                       @if($row->car)
                                                           {{$row->car->carNumber}}
                                                       @else
                                                           <span class="text-muted">Not Assigned</span>
                                                       @endif
                                                   </td>
                                                   <td><div class="form-inline">
                                                <div class = "input-group"> 
                                            <a href="{{ route('driver.edit', $row->id)}}" class="btn btn-outline-primary btn-xs" title="Edit"><i class="icon-pencil7"></i></a> 
                                            
                                            @if(!$row->is_approved)
                                                <a href="{{ route('driver.approve', $row->id)}}" class="btn btn-outline-success btn-xs ml-1" title="Approve & Create User"><i class="icon-cog"></i></a>
                                            @else
                                                <span class="badge badge-flat border-success text-success-600 ml-1"><i class="icon-check mr-1"></i> Approved</span>
                                            @endif
                                        </div>
                                    </div>
                                    </td>      
                                                               
                                      </tr>
                                    @endforeach
                                
                                    @endif
                                
                                   </tbody>
                                       
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade @if(!empty($id)) active show @endif" id="profile2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Sahili Dereva Mpya (Register New Driver)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-12 ">
                                                
                                                {{ Form::open(['route' => 'driver.store','role'=>'form','enctype'=>'multipart/form-data']) }}
                                                @method('POST')
                                               

                                                <div class="row">
                                                    <div
                                                        class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12 offset-lg-3">
                                                        <div class="card">

                                                            <div class="card-body">
                                                                <div class="form-group row">
                                                                    <label class="control-label">Jina la Dereva<span class="required">
                                                                            </span></label> </label>
                                                                    <input type="text" required name="name" class="form-control" required placeholder="Weka Jina la Dereva">
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label class="control-label">Barua Pepe (si lazima)</label>
                                                                    <input type="email" name="email" class="form-control" placeholder="Weka Barua Pepe (hiari)">
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label class="control-label">Namba ya Simu ya Dereva<span class="required">
                                                                            </span></label> </label>
                                                                    <input type="tel" name="phone" class="form-control"  placeholder="Weka Namba ya Simu ya Dereva">
                                                                </div>


                                                                <div class="btn-bottom-toolbar text-right">
                                                                 
                                                                    <button type="submit"
                                                                        class="btn btn-sm btn-primary">Save</button>
                                                              
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