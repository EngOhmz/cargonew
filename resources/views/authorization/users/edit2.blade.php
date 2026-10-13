@extends('layouts.master')



@section('contents')
<section class="section">
    <div class="section-body">
        @include('layouts.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Client</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">

                            <a href="{{route('clients.index')}}" class="btn btn-secondary btn-xs px-4">
                                <i class="fa fa-arrow-alt-circle-left"></i>
                                Back
                            </a>


                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                @foreach($user as $users)
            {{ Form::model($users, array('route' => array('clients.update', $users->id), 'method' => 'PUT')) }}
            <div class="ibox-content p-0 px-3 pt-2">
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Full Name</label>
                        <input type="text" class="form-control" name="fname" id="fnname" value="{{$users->fname}}">
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Phone Number</label>
                        <input type="phone" class="form-control" name="phone" id="phone" value="{{$users->phone}}">
                    </div>
                   
                        <input type="hidden" class="form-control" name="lname" id="lame" value="client">
                    
                </div>
                <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{$users->email}}">
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Address</label>
                        <select class="form-control" name="address" required>
                            <option value="{{ old('address')}}" disabled selected>Choose option</option>
                            @foreach($region as $row)
                            <option @if($row->name == optional($users->address) ) selected @endif value="{{ $row->name }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                 <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">VAT</label>
                        <input type="text" class="form-control" name="vat" id="vat" value="{{ optional($users->userdetails)->vat }}">
                        @error('vat')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">Company Currency</label>

                        <select class="form-control1" name="currency_code" id="select-state" >
                           <option value="{{ old('currency_code')}}" disabled selected>Choose option</option>
                            @foreach($currency as $row)
                            <option @if($row->code == optional($users->userdetails)->currency_code ) selected @endif value="{{ $row->code }}">{{ $row->name }}</option>
                            @endforeach
                        </select>
                        @error('address')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                    <script type="text/javascript">
                        new TomSelect("#select-state",{
                             create: false,
                             sortField: {
                                  field: "text",
                                 direction: "asc"
                             }
                         });  
                         
                         new TomSelect("#select-state1",{
                             create: false,
                             sortField: {
                                  field: "text",
                                 direction: "asc"
                             }
                         });  
                    </script>
                </div>
                     <div class="row">
                    <div class="form-group col-lg-6 col-md-12 col-sm-12">
                        <label class="control-label">TIN</label>
                        <input type="text" class="form-control" name="tin" id="tin" value="{{ optional($users->userdetails)->tin}}">
                        @error('tin')
                        <p class="text-danger">. {{$message}}</p>
                        @enderror
                    </div>
                
                    </div>
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