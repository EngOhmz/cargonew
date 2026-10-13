@extends('layouts.master')

@section('content')

<div class="col-md-12">
    <div class="ibox">
        <div class="ibox-head">
            cash payment Request form
        </div>

        <div class="ibox-body">
                                {!! Form::open(array('route' => 'users.store','method'=>'POST','enctype','=','multipart/form-data')) !!}
                                <div class="form-group">
                                    <strong>Name:</strong>
                                    {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                                </div>
                                <div class="form-group">
                                    <strong>Email:</strong>
                                    {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!}
                                </div>
                                <div class="form-group">
                                    <strong>Password:</strong>
                                    {!! Form::password('password', array('placeholder' => 'Password','class' => 'form-control')) !!}
                                </div>
                                <div class="form-group">
                                    <strong>Confirm Password:</strong>
                                    {!! Form::password('password_confirmation', array('placeholder' => 'Confirm Password','class' => 'form-control')) !!}
                                </div>
                                <div class="form-group">
                                    <strong>Select Department:</strong>
                                    <select class="form-control" name="depId">
                                        <option>Select Department</option>
                                        <@foreach ($dep as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <strong>Attach Signature:</strong>
                                    {!! Form::file('signature', array('placeholder' => 'signature','class' => 'form-control')) !!}
                                </div>
                                <div class="form-group">
                                    <strong>Role:</strong>
                                    {!! Form::select('roles[]', $roles,[], array('class' => 'form-control','multiple')) !!}
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>


            @endsection
