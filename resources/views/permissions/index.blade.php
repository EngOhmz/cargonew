@extends('layouts.master')

@section('content')
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-head float-right">
                Permissions
                    <a class="btn btn-primary" data-toggle="modal" data-target="#createPermission" type="button" id="permission"><i
                            class="icon-copy dw dw-add"></i> New Permission</a>
               
            </div>

            <div class="ibox-body">


                <table class="table table-hover table-striped display">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>PermissionId</th>
                            <th>Name</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $x = 1;
                        @endphp
                        @foreach ($data as $permission)
                            <tr>
                                <td>{{ $x++ }}</td>
                                <td>{{ $permission->id }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <a class="btn btn-success" data-toggle="modal" data-target="#EditPermission{{ $permission->id }}"
                                        type="button" id="permissionId">Edit</a>
                                    {{-- @can('role-delete')
                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['destroy', $permission->id],
                                            'style' => 'display:inline',
                                        ]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    @endcan --}}

                                    <a class="btn btn-primary" data-toggle="modal" data-target=""
                                        type="button" id="permissionId">Show</a>

                                            <a class="btn btn-danger" data-toggle="modal" data-target=""
                                                type="button" id="permissionId">Delete</a>
                                </td>
                            </tr>

                            <!-- Edit Permission modal -->
                            <div class="modal fade" id="EditPermission{{ $permission->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myLargeModalLabel">Edit Permission</h4>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">×</button>
                                        </div>
                                        <form action="{{ route('updatePermission') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                        <div class="modal-body">
                                            <div class="lead">
                                                <strong>Name:</strong>
                                                <input type="text" class="form-control"
                                                    id="name" value="{{ $permission->name }}">
                                                    <input type="hidden" readonly class="form-control"
                                                    id="id" value="{{ $permission->id }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary"><i class="icon-copy dw dw-message-1"></i>
                                                    Submit</button>

                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Edit Permission modal -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end content Page -->

    <!-- Create Permission modal -->
    <div class="modal fade" id="createPermission" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Create Permission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'storePermission', 'method' => 'POST']) !!}
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
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
    <!-- Create Permission modal -->
@endsection
