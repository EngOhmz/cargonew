
@extends('layout.master')

@push('plugin-styles')
<!-- add some inline style or css file if any -->
{!! Html::style('plugins/table/datatable/datatables.css') !!}
{!! Html::style('plugins/table/datatable/dt-global_style.css') !!}
@endpush

@section('content')
<!-- Main Body Starts -->
<div class="layout-px-spacing">
    <div class="layout-top-spacing mb-2">
        <div class="col-md-12">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-chart-one">
                    <div class="widget-heading">
                    <h4>Add User</h4>
                     
                    </div>
                    <div class="widget-content">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">

                        <a href="{{route('users.create')}}" class="btn btn-outline-info btn-xs edit_user_btn">
                        <i class="fa fa-plus-circle"></i> Add
                    </a>


                        </ul>
                        <div class="tabs tab-content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                {{ Form::open(['route' => 'users.store']) }}
            @method('POST')
            <div class="ibox-content p-0 px-3 pt-2">
                <div class="row">
                    <div class="form-group col-lg-8 col-md-12 col-sm-12">
                        <label class="control-label">Full Name</label>
                        <input type="text" class="form-control" name="name" id="fnname" value="{{ old('fname')}}">
                        @error('name')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                  
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email')}}">
                        @error('email')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Address</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ old('address')}}">
                     
                        @error('address')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                </div>
                 
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Phone Number</label>
                        <input type="phone" class="form-control" name="phone" id="phone" value="{{ old('phone')}}">
                        @error('phone')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="">Role </label>
                        <select class="form-control" name="role">
                            <option value="" disabled selected>Choose option</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->slug }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Department</label>
                        <select  id="department_id" name="department_id" class="form-control department">
                                      <option ="">Select Department</option>
                                      @if(!empty($department))
                                                        @foreach($department as $row)

                                                        <option value="{{$row->id}}">{{$row->name}}</option>

                                                        @endforeach
                                                        @endif
                                    </select>
                    </div>


                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="">Designation </label>
                        <select id="designation_id" name="designation_id" class="form-control designation">
                                      <option>Select Designation</option>                         
                        </select>
                    </div>
            
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password">
                        @error('password')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Comfirm Password</label>
                        <input type="password" class="form-control" name="comfirmpassword" id="comfirmpassword">
                    </div>
                </div>
            </div>
            <div class="ibox-footer">
                <div class="row justify-content-end mr-1">
                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-info px-5']) !!}
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
$(document).on('click', '.edit_user_btn', function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var slug = $(this).data('slug');
    var module = $(this).data('module');
    $('#id').val(id);
    $('#p-name_').val(name);
    $('#p-slug_').val(slug);
    $('#p-module_').val(module);
    $('#editPermissionModal').modal('show');
});
</script>

<script>
    $(document).ready(function() {
    
        $(document).on('change', '.department', function() {
            var id = $(this).val();
            $.ajax({
                url: '{{url("/authorization/findDepartment")}}',
                type: "GET",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    $("#designation_id").empty();
                    $("#designation_id").append('<option value="">Select Designation</option>');
                    $.each(response,function(key, value)
                    {
                     
                        $("#designation_id").append('<option value=' + value.id+ '>' + value.name + '</option>');
                       
                    });                      
                   
                }
    
            });
    
        });
    
    
    
    
    
    
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
                "lengthMenu": [5,10,15,20],
                "pageLength": 5
            });
            $('#dropdown-dt').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5,10,15,20],
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
                "lengthMenu": [3,6,9,12],
                "pageLength": 3
            });
            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    var min = parseInt( $('#min').val(), 10 );
                    var max = parseInt( $('#max').val(), 10 );
                    var age = parseFloat( data[3] ) || 0; // use data for the age column
                    if ( ( isNaN( min ) && isNaN( max ) ) ||
                        ( isNaN( min ) && age <= max ) ||
                        ( min <= age   && isNaN( max ) ) ||
                        ( min <= age   && age <= max ) )
                    {
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
                "lengthMenu": [5,10,15,20],
                "pageLength": 5
            });
            $('#min, #max').keyup( function() { table.draw(); } );
            $('#export-dt').DataTable( {
                dom: '<"row"<"col-md-6"B><"col-md-6"f> ><""rt> <"col-md-12"<"row"<"col-md-5"i><"col-md-7"p>>>',
                buttons: {
                    buttons: [
                        { extend: 'copy', className: 'btn btn-primary' },
                        { extend: 'csv', className: 'btn btn-primary' },
                        { extend: 'excel', className: 'btn btn-primary' },
                        { extend: 'pdf', className: 'btn btn-primary' },
                        { extend: 'print', className: 'btn btn-primary' }
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
            } );
            // Add text input to the footer
            $('#single-column-search tfoot th').each( function () {
                var title = $(this).text();
                $(this).html( '<input type="text" class="form-control" placeholder="Search '+title+'" />' );
            } );
            // Generate Datatable
            var table = $('#single-column-search').DataTable({
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5,10,15,20],
                "pageLength": 5
            });
            // Search
            table.columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table = $('#toggle-column').DataTable( {
                "language": {
                    "paginate": {
                        "previous": "<i class='las la-angle-left'></i>",
                        "next": "<i class='las la-angle-right'></i>"
                    }
                },
                "lengthMenu": [5,10,15,20],
                "pageLength": 5
            } );
            $('a.toggle-btn').on( 'click', function (e) {
                e.preventDefault();
                // Get the column API object
                var column = table.column( $(this).attr('data-column') );
                // Toggle the visibility
                column.visible( ! column.visible() );
                $(this).toggleClass("toggle-clicked");
            } );
        } );
    </script>
@endpush
