<div class="modal fade" role="dialog" id="addRoleModal" aria-labelledby="addPermissionModal"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal-content">
            {{ Form::open(['route' => 'roles.store']) }}
            @method('POST')
            <div class="modal-header py-2 px-2">
                <h4 class="modal-title">ADD ROLE</h4>
            </div>
            <div class="modal-body p-3">
                <div class="form-group">
                    <label class="control-label">Role Name</label>
                    <input type="text" class="form-control" name="slug" id="p-slug_">
                </div>
            </div>
              <div class="modal-body p-3">
                <div class="form-group">
                    <label class="control-label">Role Status</label>
                    <select class="control-label" name="status" >
                    <option value="1">Public Role</option>
                    <option value="0">Private Role</option>
                    </select>
                   
                </div>
            </div>
              <div class="modal-body p-3">
                <div class="form-group">
                    <label class="control-label">Price Per Public Role</label>
                    <input type="number" class="form-control" name="price" id="p-slug_">
                </div>
            </div>
            <div class="modal-footer">
                <div class="p-2">
                     <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
                   <button class="btn btn-primary"  type="submit" id="save" ><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

</div>
