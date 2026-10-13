@extends('layouts.master')

@section('content')
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-head float-right">
                Users
                <a class="btn btn-primary" href="{{route('users.create')}}" type="button" id="permission"><i
                        class="icon-copy dw dw-add"></i> New User</a>

            </div>

            <div class="ibox-body">
                <table class="table table-hover table-striped display">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $x = 1;
                        @endphp
                        @foreach ($data as $user)
                            <tr>
                                <td>{{ $x++ }}</td>
                                <td>{{ $user->fname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a class="btn btn-success" href="{{ route('users.show', $user->id) }}"> <i
                                            class="icon-copy dw dw-eye"></i> Show</a>
                                 
                                        <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}"><i
                                                class="icon-copy dw dw-edit2"></i> Edit</a>
                                   
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Create USERS modal -->
    {{--<div class="modal fade" id="createUser" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Create Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    {!! Form::open(['route' => 'storeUser', 'method' => 'POST', 'enctype', '=', 'multipart/form-data']) !!}
                    <div class="form-group">
                        <strong>Name:</strong>
                        {!! Form::text('name', null, ['placeholder' => 'Name', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Email:</strong>
                        {!! Form::text('email', null, ['placeholder' => 'Email', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Password:</strong>
                        {!! Form::password('password', ['placeholder' => 'Password', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Confirm Password:</strong>
                        {!! Form::password('password_confirmation', ['placeholder' => 'Confirm Password', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Select Department:</strong>
                        <select class="form-control" name="depId">
                            <option>Select Department</option>
                            <@foreach ($dept as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Attach Signature:</strong>
                        {!! Form::file('signature', ['placeholder' => 'signature', 'class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        <strong>Role:</strong>
                        {!! Form::select('roles[]', $roles, [], ['class' => 'form-control', 'multiple']) !!}
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
    </div>--}}
    <!-- Create USERS modal -->
@endsection
