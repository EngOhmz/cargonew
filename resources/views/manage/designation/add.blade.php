<div class="modal fade" role="dialog" id="addPermissionModal" aria-labelledby="addPermissionModal"
     data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="modal-content">
            {{ Form::open(['route' => 'designations.store']) }}
            @method('POST')
            <div class="modal-header p-2 px-2">
                <h4 class="modal-title">ADD DESIGNATIONS</h6>
            </div>
            <div class="modal-body p-3">
                <div class="form-group">
                    <label class="">Name </label>
                    <input type="text" class="form-control" name="name" required>
                </div>
               <div class="form-group">
                    <label class="">Department Name</label>
                    <select name="department_id" class="form-control m-b"  required>
                   <option value="">Select Department</option>
                 @if(!empty($department))
                 @foreach($department as $row)
               <option value="{{$row->id}}">{{$row->name}}</option>
             @endforeach
@endif
</select>
                </div>
            </div>
            <div class="modal-footer p-0">
                <div class="p-2">
                   <button class="btn btn-primary"  type="submit" id="save" ><i class="icon-checkmark3 font-size-base mr-1"></i> Save</button>
         <button class="btn btn-link" data-dismiss="modal"><i class="icon-cross2 font-size-base mr-1"></i> Close</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
