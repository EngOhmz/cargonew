@extends('layouts.master')

@section('content')
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-head float-right">
                Roles
                <a class="btn btn-primary" data-toggle="modal" data-target="#createRole" type="button" id="permission"><i class="fa-light fa-plus"></i> New Role</a>

            </div>

            <div class="ibox-body">


                <table class="table table-hover table-striped display">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>RoleId</th>
                            <th>Name</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $x = 1;
                        @endphp
                        @foreach ($data as $key => $role)
                            <tr>
                                <td>{{ $x++ }}</td>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    
                                    {{-- <a class="btn btn-primary" data-toggle="modal" data-target="#showRole{{$role->id}}" type="button" id="permission"><i
                                                class="icon-copy dw dw-add"></i> Show</a> --}}
                                    <a class="btn btn-primary" data-toggle="modal" data-target="#editRole{{$role->id}}" type="button" id="permission"><i
                                                class="icon-copy dw dw-add"></i> Edit</a>
                                    
                                    @can('role-delete')
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'style' => 'display:inline']) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>

                            <!-- edit role modal -->
                            <div class="modal fade" id="editRole{{$role->id}}" tabindex="-1" role="dialog"
                                aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Create Role</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">×</button>
                                        </div>
                                        {{-- <div class="modal-body">
                                            {!! Form::model($role, ['route' => ['updateRole'],'method' => 'PATCH']) !!}
                                            <div class="form-group">
                                                <strong>Name:</strong>
                                                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                                            </div>
                                            <div class="form-group">
                                                <strong>Permission:</strong>
                                                <br />
                                                @foreach($permission as $value)
                                                <label>{{ Form::checkbox('permission[]', $value->id, in_array($value->id, $rolePermissions) ? true : false, array('class' => 'name')) }}
                                                    {{ $value->name }}</label>
                                                <br />
                                                @endforeach
                                            </div>
                                        </div> --}}
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary"><i class="icon-copy dw dw-cursor-2"></i> Submit</button>
                                {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- edit role modal -->
                        @endforeach
                    </tbody>
                </table>
                {{ $data->render() }}

            </div>
        </div>
    </div>

    <!-- Create role modal -->
    <div class="modal fade" id="createRole" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Create Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'roles.store', 'method' => 'POST']) !!}
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Permission:</strong>
                        <br />
                        @foreach ($permission as $value)
                            <label>{{ Form::checkbox('permission[]', $value->id, false, ['class' => 'name']) }}
                                {{ $value->name }}</label>
                            <br />
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="icon-copy dw dw-message-1"></i>
                        Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- Create role modal -->
@endsection
