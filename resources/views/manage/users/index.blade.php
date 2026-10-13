@extends('layout.master')

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

    <section class="section">
        <div class="section-body">

            <div class="row">
                <div class="col-12 col-sm-6 col-lg-12">
                    <div class="card">
                        <div class="card-header header-elements-sm-inline">
                            <h4 class="card-title">Manage Users</h4>

                            <div class="header-elements">
                                <a href="{{ route('users.create') }}" class="btn btn-outline-info btn-xs edit_user_btn">
                                    <i class="icon-add"></i> Add
                                </a>

                            </div>
                        </div>

                        </ul>
                        <div class="card-body">
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <div class="tab-pane fade @if (empty($id)) active show @endif"
                                    id="home2" role="tabpanel" aria-labelledby="home-tab2">
                                    <div class="table-responsive">
                                        <table class="table table-striped datatable-basic">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Full Name</th>

                                                    <th>Phone Number</th>
                                                    <th>User Name</th>
                                                    <th>Role</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (isset($users))
                                                    @foreach ($users as $user)
                                                        @if ($user->id > 1)
                                                            <tr>
                                                                <th>{{ $loop->iteration }}</th>
                                                                <td>{{ $user->name }}</td>

                                                                <td>{{ $user->phone }}</td>
                                                                <td>{{ $user->email }}</td>
                                                                <td>
                                                                    @if($user->roles->isNotEmpty())
                                                                        <span class="badge badge-primary">{{ ucfirst(str_replace('-', ' ', $user->roles->first()->slug)) }}</span>
                                                                    @else
                                                                        <span class="badge badge-light">No role</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="form-inline">
                                                                        <a class="btn btn-outline-info btn-xs edit_user_btn"
                                                                            href="{{ route('users.edit', $user->id) }}"><i
                                                                                class="fa fa-edit"></i> Edit</a>&nbsp

                                                                        {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                                                                        {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
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
