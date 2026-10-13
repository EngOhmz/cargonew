@extends('layout.master')



@section('content')
<section class="section">
    <div class="section-body">
        @include('layout.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                        <div class="card-header header-elements-sm-inline">
								<h4 class="card-title">Edit User</h4>
								<div class="header-elements">
								   
                             
                      <a href="{{route('users.index')}}" class="btn btn-secondary btn-xs px-4">
                                <i class="fa fa-arrow-alt-circle-left"></i>
                                Back
                            </a>
									
				                	</div>
			                	
							</div>


                    <div class="card-body">
                       
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                @foreach($user as $users)
            {{ Form::model($users, array('route' => array('users.update', $users->id), 'method' => 'PUT')) }}
            <div class="ibox-content p-0 px-3 pt-2">
                <div class="row">
                    <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <label class="control-label">Full Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{$users->name}}">
                    </div>
                 
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">User Name</label>
                        <input type="text" class="form-control" name="email" id="email" value="{{$users->email}}">
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Address</label>
                         <input type="text" class="form-control" name="address" id="address" value="{{ $users->address}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Phone Number</label>
                        <input type="phone" class="form-control" name="phone" id="phone" value="{{ $users->phone }}">
                        @error('phone')
                        <p class="text-danger">. {{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Role <span class="text-danger">*</span></label>
                        <select class="form-control" name="role_id" id="role_id" required>
                            <option value="" disabled>Chagua jukumu</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ (string) old('role_id', $userRoleId) === (string) $role->id ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $role->slug)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <p class="text-danger">. {{ $message }}</p>
                        @enderror
                        <small class="text-muted">Menyu na ruhusa za mtumiaji zitategemea jukumu hili.</small>
                    </div>
                </div>

            <div class="ibox-footer">
                <div class="row justify-content-end mr-1">
                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-info px-5']) !!}
                </div>
            </div>
            {!! Form::close() !!}
            @endforeach
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

@endsection