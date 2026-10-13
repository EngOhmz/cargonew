@extends('layouts.master')


@section('content')
<section class="section">
    <div class="section-body">
        @include('layouts.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Roles</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                           
                            <button type="button" class="btn btn-outline-info btn-xs px-4"
                            data-toggle="modal" data-target="#addRoleModal">
                        <i class="fa fa-plus-circle"></i>
                        Add
                    </button>


                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1">
                                        <thead>
                                        <tr>
                        <th>S/N</th>
                        <th>Name</th>
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
                                <td align="right">
                                    <a href="{{ route('roles.show',$role->id) }}"
                                       class="btn btn-outline-info btn-xs"><i class="fas fa-plus-circle pr-1"></i> Assign </a>
                                </td>
                                <td align="right">
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
