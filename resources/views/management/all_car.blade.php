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
                    <h4>Orodha za Magari</h4>
                </div>
                <div class="card-body">
                    <!-- Tabs within a box -->
                    

                    <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link @if(empty($id)) active show @endif" href="#home2"
                                    data-toggle="tab">Magari Yote</a>
                            </li>
                    </ul>

                    <div class="tab-content tab-bordered">
                            <!-- ************** general *************-->
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2">

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="statusFilter"><strong>Hali ya Gari</strong></label>
                                        <select id="statusFilter" class="form-control">
                                            <option value="">Hali Zote</option>
                                            <option value="0">Ipo njiani</option>
                                            <option value="1">Imefika</option>
                                            <option value="2">Inapakiwa</option>
                                            <option value="3">Imefungwa</option>
                                            <option value="5">Inasubiri</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="sourceFilter"><strong>Kutoka (Source)</strong></label>
                                        <select id="sourceFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}">{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="destinationFilter"><strong>Kwenda (Destination)</strong></label>
                                        <select id="destinationFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}">{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button id="applyFilters" class="btn btn-primary w-100">Chuja Magari</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped data-table" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Namba ya Gari</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
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
<script type="text/javascript">
$(function() {
    $('.select-search').select2();

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('all_car') }}",
            data: function (d) {
                d.source = $('#sourceFilter').val();
                d.destination = $('#destinationFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'carNumber', name: 'carNumber' },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    switch (parseInt(data, 10)) {
                        case 0: return 'Ipo njiani';
                        case 1: return 'Imefika';
                        case 2: return 'Inapakiwa';
                        case 3: return 'Imefungwa';
                        case 5: return 'Inasubiri';
                        default: return data;
                    }
                }
            },
            { 
                data: 'id',
                name: 'action',
                render: function(data, type, row) {
                    return '<a href="{{route("car_today_routes", ":id")}}" class="btn btn-outline-primary btn-xs" title="Routes">Routes</a>'.replace(':id', data);
                },
                orderable: false,
                searchable: false
            }
        ],
        language: {
            search: "Tafuta Namba ya Gari:",
            searchPlaceholder: "Andika namba ya gari...",
            lengthMenu: "Onyesha _MENU_ magari kwa ukurasa",
            info: "Onyesha _START_ hadi _END_ ya _TOTAL_ magari",
            paginate: {
                first: "Ya Kwanza",
                last: "Ya Mwisho", 
                next: "Ifuatayo",
                previous: "Iliyotangulia"
            }
        }
    });

    // Filter by status column (index 2)
    $('#statusFilter').on('change', function() {
        var val = this.value;
        table.column(2).search(val).draw();
    });

    // Re-draw table when source or destination filters change
    $('#applyFilters').on('click', function() {
        table.draw();
    });

    $.fn.dataTable.ext.errMode = 'none';
    $('.data-table').on('error.dt', function ( e, settings, techNote, message ) {
        console.log('An error has been reported by DataTables: ', message);
    });
});
</script>
@endpush
