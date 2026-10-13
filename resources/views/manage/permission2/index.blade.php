@extends('layouts.master')


@section('content')
<section class="section">
    <div class="section-body">
        @include('layouts.alerts.message')
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Manage Permissions</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab2" role="tablist">
                           
                        <button type="button" class="btn btn-outline-info btn-xs px-4"
                            data-toggle="modal" data-target="#addPermissionModal">
                        <i class="fa fa-plus-circle"></i>
                        Add
                    </button>


                        </ul>
                        <div class="tab-content tab-bordered" id="myTab3Content">
                            <div class="tab-pane fade @if(empty($id)) active show @endif" id="home2" role="tabpanel"
                                aria-labelledby="home-tab2">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-1">
                                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Slug</th>
                        <th>Module</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($permissions))
                    @foreach($permissions as $permission)
                    <?php $f = $permission->slug;  ?>
                    @can($f)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $permission->slug }}</td>
                            <td>{{ $permission->modules->slug  ?? '' }}</td>
                            <td align="right">
                                {!! Form::open(['route' => ['permissions.destroy', $permission->id], 'method' => 'delete']) !!}
                                <button type="button" class="btn btn-outline-info btn-xs edit_permission_btn"
                                        data-toggle="modal"
                                        data-id="{{$permission->id}}"
                                        data-name="{{$permission->name}}"
                                        data-slug="{{$permission->slug}}"
                                        data-module="{{ isset($permission->modules->id) ? $permission->modules->id:'' }}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                {{ Form::button('<i class="fas fa-trash"></i> Delete', ['type' => 'submit', 'class' => 'btn btn-outline-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                    @endcan
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


@include('manage.permission.add')
@include('manage.permission.edit')

@endsection

@section('scripts')
<script>
        $(document).on('click', '.edit_permission_btn', function () {
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
