@extends('layouts.master')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="fa fa-pencil"></i></a>
                            <div class="text-center">
                                {{-- <img class="profile-user-img img-fluid img-circle" style="height: 200px" src="images/photo4.jpg"
                                    alt="User profile picture"> --}}
                                    <img src="{{ asset('user_images/'.Auth::user()->image) }}" class="profile-user-img img-fluid img-circle" alt="" title="" style="height: 200px">
                            </div>
                            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body pd-5">
                                            <div class="img-container">
                                               <form action="/updatePicture/{{ Auth::user()->id }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label>Update Profile</label>
                                                    <div class="custom-file">
                                                        <input type="file" name="image" class="form-control-file form-control height-auto">
                                                    </div>
                                                </div>
                                               
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary"><span> Update</span></button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                    </div>
                                </div>
                            </div>

                            <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                            <p class="text-center text-muted font-14">
                                @if(!empty(Auth::user()->getRoleNames()))
                                @foreach(Auth::user()->getRoleNames() as $val)
                                <label class="badge badge-info">{{ $val }}</label>
                                @endforeach
                                @endif
                            </p>
                            <div class="profile-show">
                                <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                <div class="row">
                                    <div class="col-6">
                                        Email
                                    </div>
                                    <div class="col-6">
                                        {{ Auth::user()->email }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        Phone Number
                                    </div>
                                    <div class="col-6">
                                        {{ Auth::user()->contact }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        Address
                                    </div>
                                    <div class="col-6">
                                        {{ Auth::user()->address }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        Signature
                                    </div>
                                    <div class="col-6">
                                      
                                        <img src="{{ asset('images/'.Auth::user()->signature) }}" alt="" title=""  style="height:100px">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        Status
                                    </div>
                                    <div class="col-6">
                                        @if (( Auth::user()->is_active ) === 1)
                                        <button class="badge rounded-pill bg-success">Active</button>
                                        @elseif (( Auth::user()->is_active ) === 2)
                                        <button class="badge rounded-pill bg-danger">Deactiveted</button>
                                        @else
                                        <button class="badge rounded-pill bg-warning">Leave</button>
                                        @endif
                                    </div>
                                </div>
                            </div>


                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#profileDetails"
                                        data-toggle="tab">Profile Details</a></li>
                                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Change
                                        Password</a>
                                </li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active tab-pane" id="profileDetails">
                                    <form action="/updateUserInfo/{{ Auth::user()->id }}" method="POST" enctype="multipart/form-data" >
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Email</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" name="email" value="{{ Auth::user()->email }}" type="email" readonly>
                                            </div>
                                        </div>
        
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Full Name</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" name="name" type="text" value="{{ Auth::user()->name }}">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Contact</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" value="{{ Auth::user()->contact }}" type="text" name="contact">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Gender</label>
                                            <div class="col-sm-12 col-md-10">
                                                <select class="custom-select col-12" name="gender">
                                                    <option selected="">{{ Auth::user()->gender }}</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-12 col-md-2 col-form-label">Address</label>
                                            <div class="col-sm-12 col-md-10">
                                                <input class="form-control" value="{{ Auth::user()->address }}" type="text" name="address">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <strong>Attach Signature:</strong>
                                            {!! Form::file('signature', array('placeholder' => 'signature','class' => 'form-control')) !!}
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-12 col-md-10">
                                                <button type="submit" class="btn btn-primary"><span> Save changes</span></button>
                                            </div>
                                        </div>
        
                                    </form>
                                    
                                </div>

                                <div class="tab-pane" id="settings">
                                    {{-- <form class="form-horizontal" action="{{ route('updatePass') }}" method="POST">
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Old Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" name="oldPassword" placeholder="Old Password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">New Password</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" name="newPassword" placeholder="New Password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Comfirm Password</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="comformPassword" placeholder="Comfirm Password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form> --}}



                                    <div class="card">
                                        <div class="card-header">{{ __('Chnage Password') }}</div>
                    
                                        <form action="{{ route('updatePass') }}" method="POST">
                                            @csrf
                                            <div class="card-body">
                                                @if (session('status'))
                                                    <div class="alert alert-success" role="alert">
                                                        {{ session('status') }}
                                                    </div>
                                                @elseif (session('error'))
                                                    <div class="alert alert-danger" role="alert">
                                                        {{ session('error') }}
                                                    </div>
                                                @endif
                    
                                                <div class="mb-3">
                                                    <label for="oldPasswordInput" class="form-label">Old Password</label>
                                                    <input name="old_password" type="password" class="form-control"  id="password" 
                                                        placeholder="Old Password" required/>
                                                        
                                                </div>
                                               
                                                <div class="mb-3">
                                                    <label for="newPasswordInput" class="form-label">New Password</label>
                                                    <input name="new_password" type="password" class="form-control" id="password" 
                                                        placeholder="New Password" required pattern="[A-Za-z0-9]{8}"/> 
                                                   
                                                </div>
                                                <div class="mb-3">
                                                    <label for="confirmNewPasswordInput" class="form-label">Confirm New Password</label>
                                                    <input name="new_password_confirmation" type="password" class="form-control" id="password" 
                                                        placeholder="Confirm New Password" required pattern="[A-Za-z0-9]{8}"/>
                                                       
                                                </div>
                    
                                            </div>
                    
                                            <div class="card-footer">
                                                <button class="btn btn-primary">Submit</button>
                                            </div>
                    
                                        </form>
                                    </div>
                                </div>
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
