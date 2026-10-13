@extends('layouts.master')


@section('content')
<section class="section">
    <div class="section-body">
        
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                    <h3 class="text-uppercase">{{ $role->slug }} ( Role ) - Permissions</h3>
                <div class="ibox-tools text-white">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-info btn-xs px-4"><i
                            class="fa fa-arrow-circle-left"></i> Back </a>
                </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">

                            <button type="button" class="btn btn-outline-info btn-xs px-4" data-toggle="modal"
                                data-target="#addRoleModal">
                                <i class="fa fa-plus-circle"></i>
                                Add
                            </button>


                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                {!! Form::open(['route' => 'roles.create']) !!}
                @method('GET')
                <table class="table table-sm table-bordered w-100" id="datatable">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Module</th>
                        <th>CRUD</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php   $i = 1 ?>
                    @foreach($modules as $module)
                    <?php $m = $module->slug  ?>
                  
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td width="25%">{{ $module->slug }}</td>
                            <td>
                               <div class="row">
                               
                                    @foreach($permissions as $permission)
                                    <?php $p = $permission->slug  ?>
                                 
                                        @if($permission->sys_module_id == $module->id)
                                            @if($role->hasAccess($permission->slug))
                                            <div class="col-md-4 col-sm-6">
                                                <label>
                                                    <input type="checkbox"
                                                        value="{{ $permission->id }}"
                                                        name="permissions[]" checked>
                                                    {{ $permission->slug }}
                                                </label>
                                            </div>
                                            @else
                                            <div class="col-md-4 col-sm-6">
                                                <label>
                                                    <input type="checkbox" value="{{ $permission->id }}"
                                                        name="permissions[]">
                                                    {{ $permission->slug }}
                                                </label>
                                            </div>
                                            @endif
                                        @endif
                                  
                                    @endforeach
                               </div>
                            </td>
                        </tr>
            
                    @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="role_id" value="{{$role->id}}">
                <div class="row justify-content-end p-0 mr-1">
                    <div class="p-1">
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-xs px-4"><i
                                class="fa fa-arrow-circle-left"></i> Back </a>
                        {!! Form::submit('Assign', ['class' => 'btn btn-outline-success btn-xs px-4']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
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
$(document).on('click', '.edit_role_btn', function() {
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