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
                        <h4>Tafuta kwa kutumia jina la mteja</h4>
                               
                    </div>
                    <div class="card-body">
                        
                        <!-- Tabs within a box -->


                        <div class="tab-content tab-bordered">
                            <!-- ************** general *************-->
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2">

                                <div class="table-responsive">
                                    <table class="table table-striped data-table">
                                        <thead>
                                            <tr>
                                            <th>No#</th>
                                                <th>Mteja</th>
                                                <th>REF No#</th>
                                                <th>Mpokeaji</th>
                                                <th>Mwandishi</th>
                                                <th>Tarehe</th>                           
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

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



    var table = $('.data-table').DataTable({

        processing: true,

        serverSide: true,

        ajax: "{{ route('search_store') }}",

        columns: [

            {
                data: 'id',
                name: 'id'
            },

            {
                data: 'mteja',
                name: 'mteja'
            },

            {
                data: 'delivery',
                name: 'delivery'
            },

            {
                data: 'mpokeaji',
                name: 'mpokeaji'
            },

            {
                data: 'added_by',
                name: 'added_by'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },

        ]

    });

    $.fn.dataTable.ext.errMode = 'none';

    $('#table').on( 'error.dt', function ( e, settings, techNote, message ) {
    console.log( 'An error has been reported by DataTables: ', message );
    } ) ;



});
</script>
@endpush