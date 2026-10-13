@extends('layouts.master')

@section('title')
    <h2><i class="fas fa-th-large pr-2 text-info"></i>User Management</h2>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <strong>Home</strong>
        </li>
        <li class="breadcrumb-item active">
            <strong>Users</strong>
        </li>
    </ol>
@endsection

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
 
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
  <script>
    jQuery(document).ready(function($) {
      $('#example').DataTable(
        {
        dom: 'Bfrtip',
        buttons: [
                    'copy',
                    'excel',
                    'csv',
                    'pdf',
                    'print'
                ],
        }
      );
     
    } );
    </script>
    
    
<section class="section">
    <div class="section-body">
        
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                   <div class="card-header header-elements-sm-inline">
                        <h4 class="card-title">Manage Users</h4>
                 
                  <div class="header-elements">                           
                         <a href="{{route('users.create')}}" class="btn btn-outline-info btn-xs edit_user_btn">
                        <i class="fa fa-plus-circle"></i> Add
                    </a>

   </div></div>

                        </ul>
                          <div class="card-body">
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                   <table class="table table-striped" id="example">
                                    <thead>
                                    <tr>
                        <th>S/N</th>
                        <th>Full Name</th>
                        
                        <th>Phone Number</th>
                        <th>User Name</th>
                        <th>Company Name</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($users))
                    @foreach($users as $user)
                    @php $role = "";  @endphp
                    @foreach($user->roles as $value2)
                    @php $role = $value2->id  @endphp
                    @endforeach
                    @php $cp_name_list = App\Models\System::where('added_by', $user->added_by)->get()  @endphp
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $user->name }}</td>
                            
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->email }}</td>
                            @if(!empty($cp_name_list))
                            <td>
                                @foreach($cp_name_list as $cp_name)
                                {{ $cp_name->name }}&nbsp;
                                @endforeach
                            </td>
                            @else
                            <td>
                                No Compnay Name Registered
                            </td>
                            @endif
                            <td>
                                @foreach($user->roles as $value2)
                                {{ $value2->slug }}
                            @endforeach
                            </td>

                            <td>
                         <div class="form-inline">
                           <a class="btn btn-outline-info btn-xs edit_user_btn"   href="{{ route('users.edit', $user->id)}}"><i class="fa fa-edit"></i> Edit</a>&nbsp

                                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}                                    
                                {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                {{ Form::close() }}
                            </div>
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
</section>



@endsection

@section('scripts')
<script>
        $(document).on('click', '.edit_user_btn', function () {
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
       $('.datatable-basic').DataTable({
            autoWidth: false,
            "columnDefs": [
                {"targets": [1]}
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
@endsection
