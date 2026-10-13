@extends('layouts.master')


@section('content')
<section class="section">
    <div class="section-body">

        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                  <div class="card-header">
                        <h3 class="text-uppercase">{{ $role->slug }} ( Role ) - Permissions</h3>
                        
   <div class="card-header header-elements-sm-inline">
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-info btn-xs px-4"><i
                                    class="fa fa-arrow-circle-left"></i> Back </a>
                      
                  <div class="header-elements">
			 <button type="button" class="btn btn-outline-info btn-xs px-4" data-toggle="modal"
                                data-target="#addRoleModal">
                                <i class="fa fa-plus-circle"></i>
                                Add
                            </button>
									
				                	</div>
                    </div>
</div>

                    <div class="card-body">
                        
                     
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
                                            <?php   $i = 1; ?>
                                            @if(auth()->user()->id == 1)
                                            <?php   $flag = 0;  ?>
                                            @else
                                            <?php   $flag = 1;  ?>
                                            @endif
                                            @foreach($modules as $module)
                                            <?php $m = $module->slug;  ?>
                                            @if( Gate::check($m) || auth()->user()->id == 1)


                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td width="15%">{{ $module->name }}


                                                </td>
                                                <td>
                                                    <div class="row">
                                                    <p>
                                                    <label class="custom-control custom-control-secondary custom-checkbox mb-2">
													<input onClick="setAllCheckboxes('actors{{$i}}', this);" type="checkbox" class="custom-control-input">
													<span class="custom-control-label">All</span>
												</label>    
                                                    </p>
                                                    </div>
                                                    <br>
                                                    <div class="row checkboxes" id="actors{{$i}}">
                                                       

                                                        @foreach($permissions as $permission)
                                                        <?php $p = $permission->slug  ?>
                                                        @if( Gate::check($p) || auth()->user()->id == 1)



                                                        @if($permission->sys_module_id == $module->id)
                                                        @if($role->hasAccess($permission->slug))
                                                        <div class="col-md-3 col-sm-6">
                                                        <label class="custom-control custom-control-secondary custom-checkbox mb-2">
													<input  type="checkbox"  value="{{ $permission->id }}"  name="permissions[]" class="custom-control-input" checked>
													<span class="custom-control-label">{{ $permission->slug }}</span>
												</label> 

                                                        </div>
                                                        @else
                                                        <div class="col-md-3 col-sm-6">
                                                        <label class="custom-control custom-control-secondary custom-checkbox mb-2">
													<input  type="checkbox"  value="{{ $permission->id }}"  name="permissions[]" class="custom-control-input">
													<span class="custom-control-label">{{ $permission->slug }}</span>
												</label>
                                                
                                                        </div>
                                                        @endif
                                                        @endif

                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>

                                            @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="role_id" value="{{$role->id}}">
                                    <div class="row justify-content-end p-0 mr-1">
                                        <div class="p-1">
                                            <a href="{{ route('roles.index') }}"
                                                class="btn btn-outline-secondary btn-xs px-4"><i
                                                    class="fa fa-arrow-circle-left"></i> Back </a>
                                            {!! Form::submit('Assign', ['class' => 'btn btn-outline-success btn-xs
                                            px-4']) !!}
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
<script>
function setAllCheckboxes(divId, sourceCheckbox) {
    divElement = document.getElementById(divId);
    inputElements = divElement.getElementsByTagName('input');
    for (i = 0; i < inputElements.length; i++) {
        if (inputElements[i].type != 'checkbox')
            continue;
        inputElements[i].checked = sourceCheckbox.checked;
    }
}
</script>

@include('manage.role.add')
@include('manage.role.edit')

@endsection

@section('scripts')
<script type="text/javascript">
function selects(e) {
    $id = 'checkbox'.e;
    var ele = document.getElementById('checkbox');
    for (var i = 0; i < ele.length; i++) {
        if (ele[i].type == 'checkbox')
            ele[i].checked = true;
    }
}

function deSelect(e) {
    var ele = document.getElementById('checkbox');
    for (var i = 0; i < ele.length; i++) {
        if (ele[i].type == 'checkbox')
            ele[i].checked = false;

    }
}
</script>
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