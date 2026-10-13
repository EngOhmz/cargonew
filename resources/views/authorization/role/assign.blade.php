@extends('layout.master')

@push('plugin-styles')
    <!-- add some inline style or css file if any -->
    {!! Html::style('plugins/table/datatable/datatables.css') !!}
    {!! Html::style('plugins/table/datatable/dt-global_style.css') !!}
@endpush

@section('content')
    <!-- Main Body Starts -->
    <div class="card">
        <div class="layout-px-spacing">
            <div class="layout-top-spacing mb-2">
                <div class="col-md-12">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                        <div class="widget widget-chart-one">
                            <div class="widget-heading">
                                <h3 class="text-uppercase">{{ $role->slug }} ( Role ) - Permissions</h3>

                            </div>
                            <div class="widget-content">
                                <div class="float-right " style="margin-bottom:20px">

                                        <button href="{{ route('roles.index') }}" class="btn btn-outline-info btn-xs px-4"><i
                                                class="icon-arrow-left7"></i> Back </button>
                                                
                                        <button type="button" class="btn btn-outline-info btn-xs px-4" data-toggle="modal"
                                        data-target="#addRoleModal">
                                        <i class="icon-add"></i>
                                        Add
                                    </button>


                                </div>

                                <div class="tabs tab-content">
                                    <div class="tab-pane fade @if (empty($id)) active show @endif"
                                        id="home2" role="tabpanel" aria-labelledby="home-tab2">
                                        <div class="table-responsive">
                                            {!! Form::open(['route' => 'roles.create']) !!}
                                            @method('GET')
                                            <table class="table table-sm table-bordered w-100" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Module</th>
                                                        <th>CRUD</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($modules->where('parent_id', null) as $module)
                                                        <tr class="bg-light">
                                                            <td class="font-weight-bold">{{ $module->slug }}</td>
                                                            <td>
                                                                <div class="row">
                                                                    @foreach ($permissions->where('sys_module_id', $module->id) as $permission)
                                                                        <div class="col-md-3">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" class="custom-control-input" id="perm_{{ $permission->id }}" value="{{ $permission->id }}" name="permissions[]" {{ $role->hasPermissionId($permission->id) ? 'checked' : '' }}>
                                                                                <label class="custom-control-label" for="perm_{{ $permission->id }}">{{ ucfirst($permission->slug) }}</label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @foreach ($modules->where('parent_id', $module->id) as $subModule)
                                                            <tr>
                                                                <td class="pl-5">{{ $subModule->slug }}</td>
                                                                <td>
                                                                    <div class="row">
                                                                        @foreach ($permissions->where('sys_module_id', $subModule->id) as $permission)
                                                                            <div class="col-md-3">
                                                                                <div class="custom-control custom-checkbox">
                                                                                    <input type="checkbox" class="custom-control-input" id="perm_{{ $permission->id }}" value="{{ $permission->id }}" name="permissions[]" {{ $role->hasPermissionId($permission->id) ? 'checked' : '' }}>
                                                                                    <label class="custom-control-label" for="perm_{{ $permission->id }}">{{ ucfirst($permission->slug) }}</label>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <input type="hidden" name="role_id" value="{{ $role->id }}">
                                            <div class="row justify-content-end p-0 mr-1">
                                                <div class="p-1">
                                                    <a href="{{ route('roles.index') }}"
                                                        class="btn btn-outline-secondary btn-xs px-4"><i
                                                            class="fa fa-arrow-circle-left"></i> Back </a>
                                                    {!! Form::submit('Assign', [
                                                        'class' => 'btn btn-outline-success btn-xs
                                                                                                                                                    px-4',
                                                    ]) !!}
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
    </div>
    @include('authorization.role.add')
    @include('authorization.role.edit'))
    <!-- Main Body Ends -->
@endsection

<!-- push external js if any -->
@push('plugin-scripts')
    {!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/table/datatable/datatables.js') !!}
    <!--  The following JS library files are loaded to use Copy CSV Excel Print Options-->
    {!! Html::script('plugins/table/datatable/button-ext/dataTables.buttons.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/jszip.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/buttons.html5.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/buttons.print.min.js') !!}
    <!-- The following JS library files are loaded to use PDF Options-->
    {!! Html::script('plugins/table/datatable/button-ext/pdfmake.min.js') !!}
    {!! Html::script('plugins/table/datatable/button-ext/vfs_fonts.js') !!}
@endpush

@push('custom-scripts')
    <script>
        $(document).on('click', '.edit_role_btn', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let slug = $(this).data('slug');
            console.log("here");
            $('#r-id_').val(id);
            $('#r-slug_').val(slug);
            $('#r-name_').val(name);
            $('#editRoleModal').modal('show');
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#basic-dt').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5, 10, 15, 20],
                "pageLength": 5
            });
            $('#dropdown-dt').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5, 10, 15, 20],
                "pageLength": 5
            });
            $('#last-page-dt').DataTable({
                "pagingType": "full_numbers",
                "language": {
                    "paginate": {
                        "first": "<i class='las la-angle-double-left'></i>",
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>",
                        "last": "<i class='las la-angle-double-right'></i>"
                    }
                },
                "lengthMenu": [3, 6, 9, 12],
                "pageLength": 3
            });
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = parseInt($('#min').val(), 10);
                    var max = parseInt($('#max').val(), 10);
                    var age = parseFloat(data[3]) || 0; // use data for the age column
                    if ((isNaN(min) && isNaN(max)) ||
                        (isNaN(min) && age <= max) ||
                        (min <= age && isNaN(max)) ||
                        (min <= age && age <= max)) {
                        return true;
                    }
                    return false;
                }
            );
            var table = $('#range-dt').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5, 10, 15, 20],
                "pageLength": 5
            });
            $('#min, #max').keyup(function() {
                table.draw();
            });
            $('#export-dt').DataTable({
                dom: '<"row"<"col-md-6"B><"col-md-6"f> ><""rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
                buttons: {
                    buttons: [{
                            extend: 'copy',
                            className: 'btn btn-primary'
                        },
                        {
                            extend: 'csv',
                            className: 'btn btn-primary'
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-primary'
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-primary'
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-primary'
                        }
                    ]
                },
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [7, 10, 20, 50],
                "pageLength": 7
            });
            // Add text input to the footer
            $('#single-column-search tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title +
                    '" />');
            });
            // Generate Datatable
            var table = $('#single-column-search').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5, 10, 15, 20],
                "pageLength": 5
            });
            // Search
            table.columns().every(function() {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            var table = $('#toggle-column').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5, 10, 15, 20],
                "pageLength": 5
            });
            $('a.toggle-btn').on('click', function(e) {
                e.preventDefault();
                // Get the column API object
                var column = table.column($(this).attr('data-column'));
                // Toggle the visibility
                column.visible(!column.visible());
                $(this).toggleClass("toggle-clicked");
            });
        });
    </script>
@endpush
