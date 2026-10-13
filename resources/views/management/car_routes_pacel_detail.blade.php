@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}

<style>
    .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        display: block;
        width: 100%;
    }
    .table-responsive table {
        width: 100%;
        min-width: 800px;
    }
</style>

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
                        <p>{{$dt}} @ dar| 1</p>
                               
                    </div>
                    <div class="card-body">
                        <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link @if(empty($id)) active show @endif" href="#home2"
                                    data-toggle="tab">MANIFEST</a>
                            </li>
                            <li class="nav-item"><a class="nav-link @if(!empty($id)) active show @endif"
                                    href="#profile2" data-toggle="tab">EVENTS</a>
                            </li>
                    </ul>


                        <div class="tab-content tab-bordered">
                            <!-- ************** general *************-->
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="sourceFilter"><strong>Kutoka (Source)</strong></label>
                                        <select id="sourceFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}" {{ request('source') == $m->regional_name ? 'selected' : '' }}>{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="destinationFilter"><strong>Kwenda (Destination)</strong></label>
                                        <select id="destinationFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}" {{ request('destination') == $m->regional_name ? 'selected' : '' }}>{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button id="applyFilters" class="btn btn-primary w-100">Chuja Mizigo</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row"><p>&nbsp;&nbsp;Mizigo => @if($total_pacel){{number_format($total_pacel)}} @else {{number_format('0')}}  @endif </p></div>
                                        <div class="row"><p>&nbsp;&nbsp;Tarajiwa => @if($total){{number_format($total,2)}} @else {{number_format('0',2)}}  @endif tsh/=</p></div>
                                        <div class="row"><p>&nbsp;&nbsp;kusanywa => @if($total_paid){{number_format($total_paid,2)}} @else {{number_format('0',2)}}  @endif tsh/=</p></div>
                                        <div class="row">
                                            <a href="{{ route('route_report', [$carId, $dt, $dt34]) }}" class="btn btn-info btn-sm">Ripoti ya Route (Mapato vs Matumizi)</a>
                                        </div>
                                    </div>
                                    <div class="col-md-6" >
                                        <div class="row">
                                      <!--  <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> -->
                                        
                                         @if(count($pacelUnique) >= 1 && count($pacelUnique) <=  80)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a>
                                        
                                        @elseif(count($pacelUnique) >= 81 && count($pacelUnique) <=  160)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> 
                                        
                                        @elseif(count($pacelUnique) >= 161 && count($pacelUnique) <=  240)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice23', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 3</a> 
                                        
                                        @elseif(count($pacelUnique) >= 241 && count($pacelUnique) <=  320)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice23', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 3</a>  &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice24', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 4</a>
                                        
                                        @elseif(count($pacelUnique) >= 321 && count($pacelUnique) <=  400)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice23', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 3</a>  &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice24', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 4</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice25', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 5</a>
                                        
                                        @elseif(count($pacelUnique) >= 401 && count($pacelUnique) <=  480)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice23', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 3</a>  &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice24', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 4</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice25', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 5</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice26', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 6</a>
                                        
                                        @elseif(count($pacelUnique) >= 481 && count($pacelUnique) <=  560)
                                        <a target="_blank" href="{{ route('car_invoice', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice22', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice23', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 3</a>  &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_invoice24', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 4</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice25', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 5</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice26', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 6</a>
                                        
                                        <a target="_blank" href="{{ route('car_invoice27', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices 7</a>
                                        
                                        @else
                                        <a target="_blank" href="{{ route('car_invoiceAll', [$carId, $dt, $dt34])}}" class="edit btn btn-light btn-xs">Print Invoices All</a> 
                                        @endif
                                        
                                        </div>
                                        
                                        <br>
                                        
                                        <div class="row">
                                        

                                        @if(count($pacelUnique) >= 1 && count($pacelUnique) <=  80)
                                        <a target="_blank" href="{{ route('car_manifest', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest</a>
                                        
                                        @elseif(count($pacelUnique) >= 81 && count($pacelUnique) <=  160)
                                        <a target="_blank" href="{{ route('car_manifest', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest22', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 2</a> 
                                        
                                        @elseif(count($pacelUnique) >= 161 && count($pacelUnique) <=  240)
                                        <a target="_blank" href="{{ route('car_manifest', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest22', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest23', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 3</a> 
                                        
                                        @elseif(count($pacelUnique) >= 241 && count($pacelUnique) <=  320)
                                        <a target="_blank" href="{{ route('car_manifest', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest22', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 2</a> &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest23', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 3</a>  &nbsp;&nbsp;&nbsp;
                                        
                                        <a target="_blank" href="{{ route('car_manifest24', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest 4</a>
                                        
                                        @else
                                        <a target="_blank" href="{{ route('car_manifestAll', [$carId, $dt, $dt34])}}" class="edit btn btn-danger btn-xs">Print Manifest All</a> 
                                        @endif
                                        
                                        
                                        </div>
                                    </div>

                                </div>
                                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                    <table class="table table-striped data-table" style="min-width: 100%;">

                                    <col>
                                    <col>
                                    <col>
                                    <colgroup span="8"></colgroup>
                                    <col>

                                    <tr>
                                        <th scope="col">REF N0#</th>
                                        <th scope="col">JINA</th>
                                        <th scope="col">MUDA</th>
                                        <th colspan="8" scope="colgroup" style="text-align: center;">MIZIGO</th>
                                        <th scope="col">Action</th>
                                    </tr>

                                    <tr>
                                        <td rowspan="1"></td>
                                        <td rowspan="1"></td>
                                        <td rowspan="1"></td>
                                        <th scope="col">CARGO</th>
                                        <th scope="col">QNT</th>
                                        <th scope="col">@PRICE</th>
                                        <th scope="col">PRICE</th>
                                        <th scope="col">FROM</th>
                                        <th scope="col">TO</th>
                                        <th scope="col">PAID</th>
                                        <th scope="col">R</th>
                                        <td rowspan="1"></td>
                                    </tr>

                                    @foreach($pacels as $data)
                                    @if($data->activity == "kupakia")

                                    <tr>
                                        <td>{{$data->delivery}}</td>
                                        <td>{{$data->mpokeaji}}</td>
                                        <td>{{$data->created_at->format('Y-m-d')}} @ {{$data->created_at->format('H:i:s')}}</td>
                                        <td>{{$data->name}} &nbsp;#{{$data->hashtag}}</td>
                                        <td>PLACED&nbsp;&nbsp;{{$data->idadi_kupakia}} </td>
                                        <td>{{$data->bei}}</td>
                                        <td>{{$data->jumla}}</td>
                                        <td>{{$data->mzigo_unapotoka}}</td>
                                        <td>{{$data->mzigo_unapokwenda}}</td>
                                        <td>{{$data->ela_iliyopokelewa}}</td>
                                        <td>{{$data->receipt}}</td>
                                        <td>
                                        <a target="_blank" href="{{ route('car_single_invoice', [$data->car_id, $dt, $dt34, $data->pacel_id])}}" class="edit btn btn-light btn-xs">Print Invoices</a>
                                        <a href="#" class="edit btn btn-light btn-xs">View More</a>
                                        </td>
                                    </tr>

                                    @endif

                                    @endforeach

                                        <tbody>
                                        </tbody>

                                    </table>
                                </div>
                            </div>


                            <div class="tab-pane fade @if(!empty($id)) active show @endif" id="profile2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>EVENTS</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                        </div>

                                    </div>

                                </div>

                            </div>




                        </div>


                    </div>




                </div>

            </div>
</section>

<!-- Destination Selection Modal -->
<div id="destinationModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chagua Destination kwa Manifest</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Destination (Kwenda):</label>
                    <select id="modalDestination" class="form-control select-search">
                        <option value="">Destinations Zote</option>
                        @foreach($mikoa as $m)
                            <option value="{{ $m->regional_name }}">{{ $m->regional_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Funga</button>
                <button type="button" id="confirmPrint" class="btn btn-primary">Download Manifest</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('plugin-scripts')
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
<script type="text/javascript">
    $.fn.dataTable.ext.errMode = 'none';

    $('#table').on( 'error.dt', function ( e, settings, techNote, message ) {
    console.log( 'An error has been reported by DataTables: ', message );
    } ) ;

    $(document).ready(function() {
        $('.select-search').select2();

        $('#applyFilters').on('click', function() {
            var source = $('#sourceFilter').val();
            var destination = $('#destinationFilter').val();
            var url = new URL(window.location.href);
            if (source) url.searchParams.set('source', source);
            else url.searchParams.delete('source');
            if (destination) url.searchParams.set('destination', destination);
            else url.searchParams.delete('destination');
            window.location.href = url.toString();
        });

        var currentPrintUrl = '';

        // Handle Print Manifest buttons
        $('a[href*="car_manifest"], a[href*="car_invoice"]').on('click', function(e) {
            e.preventDefault();
            currentPrintUrl = $(this).attr('href');
            var type = currentPrintUrl.includes('car_manifest') ? 'Manifest' : 'Invoices';
            $('#destinationModal .modal-title').text('Chagua Destination kwa ' + type);
            $('#destinationModal').modal('show');
        });

        $('#confirmPrint').on('click', function() {
            var selectedDest = $('#modalDestination').val();
            var url = new URL(currentPrintUrl);
            if (selectedDest) {
                url.searchParams.set('destination', selectedDest);
            }
            window.location.href = url.toString();
            $('#destinationModal').modal('hide');
        });
    });

</script>
@endpush