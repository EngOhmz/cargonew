@extends('layout.master')

@push('plugin-styles')
    {!! Html::style('plugins/flatpickr/flatpickr.css') !!}
    {!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
    <style>
        .customer-list-page .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .customer-list-page .page-subtitle {
            color: #64748b;
            font-size: 0.875rem;
            margin: 0.25rem 0 0;
        }

        .customer-list-page .filter-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .customer-list-page .filter-card label {
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
        }

        .customer-list-page .filter-card .form-control {
            min-height: 42px;
            border-radius: 8px;
            border-color: #cbd5e1;
        }

        .customer-list-page .filter-card .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .customer-list-page .data-table thead th {
            background: #2D335E;
            color: #fff;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border: none;
            white-space: nowrap;
        }

        .customer-list-page .data-table tbody td {
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .customer-list-page .ref-link {
            font-weight: 700;
            color: #4f46e5;
            text-decoration: none;
        }

        .customer-list-page .ref-link:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .customer-list-page .phone-cell {
            color: #475569;
            font-variant-numeric: tabular-nums;
        }

        .customer-list-page .location-cell {
            color: #64748b;
            max-width: 160px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .customer-list-page .date-cell {
            color: #64748b;
            white-space: nowrap;
            font-size: 0.85rem;
        }

        .customer-list-page .data-table .dropdown {
            position: static;
        }

        .customer-list-page .data-table .dropdown-menu {
            position: fixed !important;
            z-index: 1050 !important;
            min-width: 11rem;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.12);
        }

        .customer-list-page .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .customer-list-page .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
        }

        .customer-list-page .dataTables_wrapper .dataTables_filter input,
        .customer-list-page .dataTables_wrapper .dataTables_length select {
            border-radius: 8px;
            border-color: #cbd5e1;
        }
    </style>
@endpush

@section('content')
    <section class="section customer-list-page">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header page-header">
                            <div>
                                <h4 class="mb-0">Orodha ya Wateja</h4>
                                <p class="page-subtitle">Simamia na uangalie wateja wote waliosajiliwa</p>
                            </div>
                            <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                                <i class="icon-user-plus mr-1"></i> Sajili Mteja Mpya
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="filter-card">
                                <div class="row align-items-end">
                                    <div class="col-12 col-sm-6 col-md-3 mb-3 mb-md-0">
                                        <label for="start_date">Tarehe ya Kuanzia</label>
                                        <input type="text" id="start_date" class="form-control flatpickr"
                                            placeholder="Chagua tarehe" value="{{ request('start_date') }}">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-3 mb-3 mb-md-0">
                                        <label for="end_date">Tarehe ya Kuishia</label>
                                        <input type="text" id="end_date" class="form-control flatpickr"
                                            placeholder="Chagua tarehe" value="{{ request('end_date') }}">
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-2 mb-3 mb-md-0">
                                        <button id="filter_button" class="btn btn-primary w-100">
                                            <i class="icon-checkmark3 mr-1"></i> Chuja
                                        </button>
                                    </div>
                                    <div class="col-12 col-sm-6 col-md-2">
                                        <button id="clear_filter" class="btn btn-light w-100">
                                            <i class="icon-cross2 mr-1"></i> Futa
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover data-table w-100">
                                    <thead>
                                        <tr>
                                            <th>Ref No</th>
                                            <th>Mpokeaji (Ofisi)</th>
                                            <th>Namba ya Mpokeaji</th>
                                            <th>Mteja</th>
                                            <th>Namba ya Mteja</th>
                                            <th>Registered By</th>
                                            <th>Tarehe</th>
                                            <th>Vitendo</th>
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
    {!! Html::script('plugins/flatpickr/flatpickr.js') !!}
@endpush

@push('custom-scripts')
    <script type="text/javascript">
        $(function() {
            $(".flatpickr").flatpickr({ dateFormat: "Y-m-d" });

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                order: [[6, 'desc']],
                language: {
                    processing: '<i class="icon-spinner2 spinner mr-1"></i> Inapakia...',
                    emptyTable: 'Hakuna wateja walio patikana',
                    zeroRecords: 'Hakuna matokeo yanayolingana na utafutaji wako',
                    search: 'Tafuta:',
                    lengthMenu: 'Onyesha _MENU_',
                    info: 'Inaonyesha _START_ hadi _END_ kati ya _TOTAL_',
                    paginate: {
                        first: 'Kwanza',
                        last: 'Mwisho',
                        next: 'Ijayo',
                        previous: 'Iliyotangulia'
                    }
                },
                ajax: {
                    url: "{{ route('list_ya_wateja.index') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();

                        var params = new URLSearchParams(window.location.search);
                        var customerId = params.get('customer_id');
                        if (customerId) {
                            d.customer_id = customerId;
                        }
                    }
                },
                columns: [
                    { data: 'id', name: 'customers.id' },
                    { data: 'mpokeaji', name: 'customers.mpokeaji' },
                    { data: 'receiver_phone', name: 'customers.receiver_phone', className: 'phone-cell' },
                    { data: 'mteja', name: 'customers.mteja' },
                    { data: 'customer_phone', name: 'customers.customer_phone', className: 'phone-cell' },
                    { data: 'registered_by', name: 'users.name' },
                    { data: 'created_at', name: 'customers.created_at', className: 'date-cell' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $('#filter_button').on('click', function() {
                table.draw();
            });

            $('#clear_filter').on('click', function() {
                $('#start_date, #end_date').val('');
                if ($('#start_date')[0]._flatpickr) {
                    $('#start_date')[0]._flatpickr.clear();
                    $('#end_date')[0]._flatpickr.clear();
                }
                table.draw();
            });

            $.fn.dataTable.ext.errMode = 'none';
            $('.data-table').on('error.dt', function(e, settings, techNote, message) {
                console.log('DataTables error:', message);
            });

            $(document).on('click', '.customer-list-page .dropdown-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $button = $(this);
                var $menu = $button.next('.dropdown-menu');
                var isShowing = $menu.hasClass('show');

                $('.customer-list-page .dropdown-menu').removeClass('show');

                if (!isShowing) {
                    $menu.addClass('show');

                    var buttonOffset = $button.offset();
                    var buttonHeight = $button.outerHeight();
                    var buttonWidth = $button.outerWidth();
                    var menuWidth = $menu.outerWidth();
                    var windowWidth = $(window).width();
                    var windowHeight = $(window).height();
                    var scrollTop = $(window).scrollTop();
                    var scrollLeft = $(window).scrollLeft();

                    var top = buttonOffset.top + buttonHeight + scrollTop;
                    var left = buttonOffset.left + scrollLeft + buttonWidth - menuWidth;

                    if (left < scrollLeft) {
                        left = buttonOffset.left + scrollLeft;
                    }

                    var menuHeight = $menu.outerHeight();
                    if (top + menuHeight > windowHeight + scrollTop) {
                        top = buttonOffset.top - menuHeight + scrollTop;
                    }

                    $menu.css({
                        position: 'fixed',
                        top: top + 'px',
                        left: left + 'px',
                        zIndex: 1050
                    });
                }
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.customer-list-page .dropdown-menu').removeClass('show');
                }
            });

            $(window).on('scroll', function() {
                $('.customer-list-page .dropdown-menu.show').removeClass('show');
            });

            $('.table-responsive').on('scroll', function() {
                $('.customer-list-page .dropdown-menu.show').removeClass('show');
            });
        });
    </script>
@endpush
