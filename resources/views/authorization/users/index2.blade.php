@extends('layouts.master')

@section('title')
<h2><i class="fas fa-th-large pr-2 text-info"></i>User Management</h2>
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <strong>Home</strong>
    </li>
    <li class="breadcrumb-item active">
        <strong>Users</strong>
    </li>
</ol>
@endsection

@section('content')
<section class="section">
    <div class="section-body">
        @include('layouts.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Clients</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                            <a href="{{route('clients.create')}}"
                                class="btn btn-outline-info btn-xs edit_user_btn">
                                <i class="fa fa-plus-circle"></i> Add
                            </a>


                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Full Name</th>
                                               
                                                <th>Phone Number</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($users))
                                             @php
                                            $role = "";
                                            $i =1;

                                            @endphp
                                            @foreach($users as $user)
                                           
                                            @foreach($user->roles as $value2)
                                            @php $role = $value2->id @endphp
                                            @endforeach
                                            @if($role == 4)
                                            <tr>
                                                <th>{{ $i++ }}</th>
                                                <td>{{ $user->fname }}</td>
                                              
                                                <td>{{ $user->phone }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach($user->roles as $value2)
                                                    {{ $value2->slug }}
                                                    @endforeach
                                                </td>

                                                <td>
                                                    {!! Form::open(['route' => ['clients.destroy', $user->id], 'method' =>
                                                    'delete']) !!}
                                                    <a class="btn btn-outline-info btn-xs edit_user_btn"
                                                        href="{{ URL::to('clients/'.$user->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i> Edit</a>
                                                    {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                                    {{ Form::close() }}
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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