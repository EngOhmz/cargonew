<div class="modal fade" role="dialog" id="editRoleModal" aria-labelledby="editRoleModal"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal-content">
            {{ Form::open(['route' => ['roles.update', 1]])}}
            @method('PUT')
            <div class="modal-header p-2 px-2">
                <h4 class="modal-title">EDIT PERMISSION</h4>
            </div>
            <div class="modal-body p-3">
                <div class="form-group">
                    <label class="control-label">Role Name </label>
                    <input type="text" class="form-control" name="slug" id="r-slug_">
                </div>
                <input type="hidden" name="id" id="r-id_">
            </div>
            <div class="modal-footer p-0">
                <div class="p-2">
                    <button type="button" class="btn btn-sm btn-outline-warning px-3 mr-1" data-dismiss="modal">Close
                    </button>
                    {!! Form::submit('Save', ['class' => 'btn btn-sm btn-outline-success px-3']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
