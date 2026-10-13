@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('assets/css/loader.css') !!}
    {!! Html::style('plugins/apex/apexcharts.css') !!}
    {!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    {!! Html::style('assets/css/elements/tooltip.css') !!}
    
    <style>
        /* Fix dropdown menu appearing between datatable rows */
        .data-table .dropdown {
            position: static;
        }
        
        .data-table .dropdown-menu {
            position: fixed !important;
            z-index: 1050 !important;
            margin-top: 0.125rem;
            min-width: 10rem;
        }
        
        /* Allow dropdown to overflow table container */
        .table-responsive {
            position: relative;
        }
        
        .data-table {
            position: relative;
        }
        
        /* Ensure dropdown appears above table */
        .data-table tbody tr {
            position: relative;
        }
        
        .data-table .dropdown.show .dropdown-menu {
            display: block !important;
        }
        
        /* Prevent table from clipping dropdown */
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
        }
    </style>
@endpush

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>List ya Mizigo</h4>
                        </div>
                        <div class="card-body">
                            <div class="row" style="margin-bottom: 12px;">
                                <div class="col-12 col-sm-4 col-md-3">
                                    <label for="start_date">Tarehe ya Kuanzia</label>
                                    <input type="text" id="start_date" class="form-control flatpickr" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-12 col-sm-4 col-md-3">
                                    <label for="end_date">Tarehe ya Kuishia</label>
                                    <input type="text" id="end_date" class="form-control flatpickr" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-12 col-sm-4 col-md-2" style="display: flex; align-items: flex-end;">
                                    <button id="filter_button" class="btn btn-primary w-100">Chuja</button>
                                </div>
                            </div>

                            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                                <table class="table table-striped data-table">
                                    <thead>
                                        <tr>
                                            <th>Ref No</th>
                                            <th>Jina la Mzigo</th>
                                            <th>Mteja</th>
                                            <th>Mpokeaji (Ofisi)</th>
                                            <th>Hali ya Mzigo</th>
                                            <th>Tarehe</th>
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

@push('plugin-scripts')
    {!! Html::script('assets/js/loader.js') !!}
    {!! Html::script('plugins/apex/apexcharts.min.js') !!}
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
    {!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
<script type="text/javascript">
$(function() {
    // Initialize flatpickr
    $(".flatpickr").flatpickr({
        dateFormat: "Y-m-d",
    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('list_ya_mizigo.index') }}",
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'hashtag', name: 'hashtag' },
            { data: 'name', name: 'name' },
            { data: 'mteja', name: 'mteja' },
            { data: 'mpokeaji', name: 'mpokeaji' },
            { data: 'activity', name: 'activity' },
            { data: 'created_at', name: 'created_at' },
            { 
                data: 'id',
                name: 'action',
                render: function(data, type, row) {
                    return '<div class="dropdown">' +
                        '<button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton' + data + '" aria-haspopup="true" aria-expanded="false">' +
                        '<i class="icon-gear"></i>' +
                        '</button>' +
                        '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">' +
                        '<a class="dropdown-item" href="{{route("print_delivery_show", ":id")}}" target="_blank"><i class="icon-printer"></i> Print Delivery</a>'.replace(':id', data) +
                        '<a class="dropdown-item" href="{{route("list_ya_mizigo.show", ":id")}}"><i class="icon-eye"></i> View</a>'.replace(':id', data) +
                        '<a class="dropdown-item" href="{{route("pacel_delete", ":id")}}" onclick="return confirm(\'Are you sure you want to delete this parcel?\')"><i class="icon-trash"></i> Delete Mizigo</a>'.replace(':id', data) +
                        '</div>' +
                        '</div>';
                },
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#filter_button').on('click', function() {
        table.draw();
    });

    $.fn.dataTable.ext.errMode = 'none';
    $('.data-table').on('error.dt', function ( e, settings, techNote, message ) {
        console.log('An error has been reported by DataTables: ', message);
    });

    // Initialize Bootstrap dropdowns for dynamically added content
    $(document).on('click', '.dropdown-toggle', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $button = $(this);
        var $dropdown = $button.closest('.dropdown');
        var $menu = $button.next('.dropdown-menu');
        var isShowing = $menu.hasClass('show');
        
        // Close all dropdowns first
        $('.dropdown-menu').removeClass('show');
        
        if (!isShowing) {
            // Show this dropdown
            $menu.addClass('show');
            
            // Position dropdown menu using fixed positioning
            var buttonOffset = $button.offset();
            var buttonHeight = $button.outerHeight();
            var buttonWidth = $button.outerWidth();
            var menuWidth = $menu.outerWidth();
            var windowWidth = $(window).width();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var scrollLeft = $(window).scrollLeft();
            
            // Calculate position
            var top = buttonOffset.top + buttonHeight + scrollTop;
            var left = buttonOffset.left + scrollLeft;
            
            // Adjust if menu would go off screen to the right
            if (left + menuWidth > windowWidth + scrollLeft) {
                left = buttonOffset.left + scrollLeft + buttonWidth - menuWidth;
            }
            
            // Adjust if menu would go off screen at bottom
            var menuHeight = $menu.outerHeight();
            if (top + menuHeight > windowHeight + scrollTop) {
                top = buttonOffset.top - menuHeight + scrollTop;
            }
            
            // Set position
            $menu.css({
                'position': 'fixed',
                'top': top + 'px',
                'left': left + 'px',
                'z-index': 1050
            });
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Close dropdown on scroll
    $(window).on('scroll', function() {
        $('.dropdown-menu.show').removeClass('show');
    });
    
    $('.table-responsive').on('scroll', function() {
        $('.dropdown-menu.show').removeClass('show');
    });
});
</script>
@endpush



