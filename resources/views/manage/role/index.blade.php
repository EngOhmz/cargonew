@extends('layouts.master')


@section('content')
<section class="section">
    <div class="section-body">
        @include('layouts.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
 
              

<div class="card-header header-elements-sm-inline">
								<h4 class="card-title">Roles</h4>
								<div class="header-elements">
								   
                            <button type="button" class="btn btn-outline-info btn-xs px-4"
                            data-toggle="modal" data-target="#addRoleModal">
                        <i class="fa fa-plus-circle"></i>
                        Add
                    </button>
									
				                	</div>
			                	
							</div>
                        
      <div class="card-body">
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                    <table class="table datatable-basic table-striped" id="table-1">
                                        <thead>
                                        <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Role Type</th>
                        <th>Price</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                                        </thead>
                                        <tbody>
                        @foreach($roles as $role)
                        @if($role->added_by == auth()->user()->id)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $role->slug }}</td>
                                
                                 <td>
                                 @if($role->status == 1)
                                 <p>Public role</p>
                                 @else
                                 <p>Private Role</p>
                                 @endif
                                 </td>
                                
                                <td>{{$role->price}}</td>
                                <td>
                                    <a href="{{ route('roles.show',$role->id) }}"
                                       class="btn btn-outline-info btn-xs"><i class="fas fa-plus-circle pr-1"></i> Assign </a>
                                </td>
                                <td >
                                    {!! Form::open(['route' => ['roles.destroy', $role->id], 'method' => 'delete']) !!}
                                    <button type="button" class="btn btn-outline-info btn-xs edit_role_btn mr-1"
                                            data-toggle="modal"
                                            data-id="{{$role->id}}"
                                            data-name="{{$role->name}}"
                                            data-slug="{{$role->slug}}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                    {{ Form::close() }}
                                </td>
                            </tr>
                            @endif
                        @endforeach
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


@include('manage.role.add')
@include('manage.role.edit')

@endsection

@section('scripts')
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
<script>
        $(document).on('click', '.edit_role_btn', function () {
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
@endsection
