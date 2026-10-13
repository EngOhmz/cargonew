
@extends('layout.master')


@section('content')

<div class="sub-header-container">
    <header class="header navbar navbar-expand-sm">
        <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom">
            
        </a>

        
    </header>
</div>
<!-- Main Body Starts -->

<div class="section-body">
    <div class="row">
        <div class="col-12 col-sm-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    

                    <div class="col-md-6"></div> 
        <div class="col-md-3"> <button type="button" class="btn btn-outline-info btn-xs px-4 pull-right"
            data-toggle="modal" data-target="#addPermissionModal">
        <i class="fa fa-plus-circle"></i>
        Add
    </button></div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if(empty($id)) active show @endif" id="home-tab2"
                                data-toggle="tab" href="#home2" role="tab" aria-controls="home"
                                aria-selected="true">Designation
                                List</a>
                        </li>


                    </ul>
                    
                   
                        <div class="tabs tab-content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive mb-4">
                                    <table class="table datatable-basic table-striped">
                                        <thead>
                                            <th>S/N</th>
                                            <th>Name</th>
                                         <th>Department Name</th>
                                            <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($permissions))
                                            @foreach($permissions as $permission)
                                           
                                                <tr>
                                                    <th>{{ $loop->iteration }}</th>
                                                    <td>{{ $permission->name }}</td>
                                                    <td>{{ $permission->department->name }}</td>
                                                    <td>
                                                        {!! Form::open(['route' => ['designations.destroy', $permission->id], 'method' => 'delete']) !!}
                                                        <button type="button" class="btn btn-outline-info btn-xs edit_permission_btn"
                                                                data-toggle="modal"
                                                                data-id="{{$permission->id}}"
                                                         data-name="{{$permission->name}}"
                                                           data-department="{{$permission->department_id}}"
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                                        {{ Form::close() }}
                                                    </td>
                                                </tr>
                                          
                                            @endforeach
                                            @endif
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

@include('authorization.designation.add')
@include('authorization.designation.edit')

<!-- Main Body Ends -->
@endsection

@section('scripts')

 <script>
       $('.datatable-basic').DataTable({
            autoWidth: false,
            "columnDefs": [
                {"orderable": false, "targets": [1]}
            ],
           dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            "language": {
               search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
             paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' }
            },
        
        });
    </script>


<script>
      $(document).on('click', '.edit_permission_btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
             var dep = $(this).data('department');
            $('#id').val(id);
            $('#p-name_').val(name);
             $('#p-dep_').val(dep);
            $('#editPermissionModal').modal('show');
        });
</script>

@endsection
