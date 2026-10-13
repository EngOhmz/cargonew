<div class="modal fade" role="dialog" id="addPermissionModal" aria-labelledby="addPermissionModal"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal-content">
            {{ Form::open(['route' => 'departments.store']) }}
            @method('POST')
            <div class="modal-header p-2 px-2">
                <h4 class="modal-title">ADD DEPARTMENTS</h6>
            </div>
            <div class="modal-body p-3">
                <div class="form-group">
                    <label class="">Name </label>
                    <input type="text" class="form-control" name="name" required>
                </div>
              
            </div>
            <div class="modal-footer p-0">
                <div class="p-2">
                    <button type="button" class="btn btn-xs btn-outline-warning mr-1 px-3" data-dismiss="modal">Close
                    </button>
                    {!! Form::submit('Save', ['class' => 'btn btn-xs btn-outline-success px-3']) !!}
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
