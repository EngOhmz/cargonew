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
                        <h4>{{$carNumber}}</h4>
                               
                    </div>
                    <div class="card-body">
                        <!-- Tabs within a box -->

                        <ul class="list-group list-group-flush">
                        @if($dataResult->isNotEmpty())
                        @foreach($dataResult as $data)


                        <li class="list-group-item">

                        <div class="form-group">
                        <a class="btn btn-dark" href="{{ route('car_pacel_detail', [$data->car_id, $data->start_date, $data->closeDate])}}" role="button">DAR | 1</a><br><br>


                        <p class="h6">{{$data->start_date}} {{$data->closeDate}}</p>

                        <div class="row">
                        <button type="button" class="btn btn-outline-secondary">FROM  DAR | 1</button>
                        <button type="button" class="btn btn-outline-secondary">TO  {{ $data->to }} | {{ $data->driver->name }}</button>
                        <button type="button" class="btn btn-outline-secondary">NOW  DAR | 1</button>
                        <button type="button" class="btn btn-outline-secondary">MATUMIZI 0TSH/=</button>

                        </div>

                        </div>


                        </li>

                        @endforeach
                        @else
                        <li class="list-group-item">
                        <div class="form-group">
                        <p class="h6">Gari haijawai kupakia</p>
                        </div>
                        </li>
                        @endif

                        </ul>

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