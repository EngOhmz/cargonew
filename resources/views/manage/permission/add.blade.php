<div class="modal fade" role="dialog" id="addPermissionModal" aria-labelledby="addPermissionModal"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal-content">
            {{ Form::open(['route' => 'permissions.store']) }}
            @method('POST')
            <div class="modal-header p-2 px-2">
                <h4 class="modal-title">ADD PERMISSION</h6>
            </div>
            <div class="modal-body p-3">
                <div class="form-group">
                    <label class="">Module Name </label>
                    <select class="form-control m-b" name="module_id" required>
                        <option value="" disabled selected>Choose option</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->slug}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="">Permission Tag </label>
                    <input type="text" class="form-control" name="slug" required>
                </div>
            </div>
            <div class="modal-footer p-0">
                <div class="p-2">
                   <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
                   <button class="btn btn-primary"  type="submit" id="save" ><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
