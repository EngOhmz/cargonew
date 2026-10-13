@extends('layout.master')

@section('content')
    <style>
        /* Allow dropdown to overflow table container */
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible !important;
        }
    </style>
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Delivery Agent List</h4>
                            <a href="{{ route('delivery_agents.create') }}" class="btn btn-primary btn-sm">
                                <i class="icon-plus-circle2"></i> New Delivery Agent
                            </a>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped data-table w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Username (Email)</th>
                                            <th>Phone</th>
                                            <th>Source</th>
                                            <th>Destination</th>
                                            <th>Password</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('custom-scripts')
<script type="text/javascript">
$(function() {
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('delivery_agents.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'phone', name: 'phone' },
            { data: 'source_region', name: 'source_region', orderable: false, searchable: false },
            { data: 'destination_region', name: 'destination_region', orderable: false, searchable: false },
            { data: 'plain_password', name: 'plain_password' },
            { data: 'status_label', name: 'status_label', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        drawCallback: function() {
            // Re-initialize dropdowns
            $('.dropdown-toggle').dropdown();
            
            // Fix for dropdowns in responsive tables
            $('.table-responsive').on('show.bs.dropdown', function (e) {
                var $toggle = $(e.relatedTarget);
                var $menu = $toggle.next('.dropdown-menu');
                
                // Append to body to avoid clipping
                $('body').append($menu.detach());
                
                var eOffset = $toggle.offset();
                $menu.css({
                    'display': 'block',
                    'top': eOffset.top + $toggle.outerHeight(),
                    'left': eOffset.left,
                    'position': 'absolute',
                    'z-index': 10000
                });
                
                // Align right if it's a dropdown-menu-right
                if ($menu.hasClass('dropdown-menu-right')) {
                    $menu.css({
                        'left': eOffset.left + $toggle.outerWidth() - $menu.outerWidth()
                    });
                }
            });
            
            $('.table-responsive').on('hide.bs.dropdown', function (e) {
                var $toggle = $(e.relatedTarget);
                var $menu = $toggle.next('.dropdown-menu');
                // Move it back to its original container if needed, 
                // but since it's dynamic, it will be recreated anyway.
                $menu.hide();
            });
        }
    });
});
</script>
@endpush
