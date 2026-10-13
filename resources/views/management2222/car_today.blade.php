@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('assets/css/loader.css') !!}
    {!! Html::style('plugins/apex/apexcharts.css') !!}
    {!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    {!! Html::style('assets/css/elements/tooltip.css') !!}

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
                                <li class="nav-item"><a class="nav-link @if (empty($id)) active show @endif"
                                        href="#home2" data-toggle="tab">Magari Yaliyoanza kupakia</a>
                                </li>
                            </ul>

                            <div class="tab-content tab-bordered">
                                <!-- ************** general *************-->
                                <div class="tab-pane fade @if (empty($id)) active show @endif"
                                    id="home2">

                                    <div class="table-responsive">
                                        <table class="table table-striped datatable-basic" id="table-1">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Namba ya Gari</th>
                                                    <th>Jina la Dereva</th>
                                                    <th class="col-sm-3">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php use App\Models\Management\Driver;

                                                if (!empty($driverRoute)):foreach ($driverRoute as $row):
                                                    $driver = Driver::where('id', $row->driver_id)->value('name');

                                               ?>
                                                <tr class="gradeA even">
                                                    <th> </th>
                                                    <td><?php echo $row->carNumber; ?></td>
                                                    <td><?php echo $driver; ?> </td>
                                                    <td>
                                                        <div class="form-inline">
                                                            <div class="input-group">
                                                                <a href="{{ route('car_today_routes', $row->id) }}"
                                                                    class="btn btn-outline-primary btn-xs"
                                                                    title="See More">Routes</a>
                                                            </div>

                                                        </div>
                                                    </td>

                                                </tr>

                                                <?php endforeach; ?>

                                                <?php else: ?>

                                                <tr>
                                                    <td colspan="2">No data available in table </td>
                                                </tr>

                                                <?php endif; ?>

                                            </tbody>

                                        </table>
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
@section('scripts')
    <script>
        $('.datatable-basic').DataTable({
            autoWidth: false,
            "columnDefs": [{
                "targets": [1]
            }],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            "language": {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {
                    'first': 'First',
                    'last': 'Last',
                    'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                    'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                }
            },

        });
    </script>
@endsection
@push('plugin-scripts')
    {!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/apex/apexcharts.min.js') !!}
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
    {!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
@endpush
