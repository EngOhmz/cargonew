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
                    <h4>Active Cars - Magari Yanayopakiwa Sasa</h4>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs">
                            <li class="nav-item"><a
                                    class="nav-link active show" href="#home2"
                                    data-toggle="tab">Inapakiwa</a>
                            </li>
                    </ul>

                    <div class="tab-content tab-bordered">
                            <div class="tab-pane fade active show" id="home2">

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="sourceFilter"><strong>Kutoka (Source)</strong></label>
                                        <select id="sourceFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}">{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="destinationFilter"><strong>Kwenda (Destination)</strong></label>
                                        <select id="destinationFilter" class="form-control select-search">
                                            <option value="">Mikoa Yote</option>
                                            @foreach($mikoa as $m)
                                                <option value="{{ $m->regional_name }}">{{ $m->regional_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button id="applyFilters" class="btn btn-primary w-100">Chuja Magari</button>
                                    </div>
                                </div>

                                <p class="text-muted mb-2">
                                    Inaonyesha magari yenye status <strong>Inapakiwa</strong> tu (yanayopakiwa kwa muda huu).
                                </p>

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
            url: "{{ route('active_car') }}",
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
                    return 'Inapakiwa';
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
