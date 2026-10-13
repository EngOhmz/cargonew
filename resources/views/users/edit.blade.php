@extends('layouts.master')

@section('content')
    <div class="col-md-12">
        <div class="ibox">
            <div class="ibox-head">
                Users
                <a class="btn btn-primary" href="{{ route('users.index') }}">Users</a>
            </div>
            <div class="ibox-body">
                {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'PATCH']) !!}
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
                        
                        <@foreach ($dep as $item)
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
                    {!! Form::select('roles[]', $roles, $userRole, ['class' => 'form-control', 'multiple']) !!}
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection