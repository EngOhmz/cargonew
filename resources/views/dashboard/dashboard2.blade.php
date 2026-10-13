@extends('layout.master')


@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}
{!! Html::style('assets/css/cargo-route-select.css') !!}


<style>
#loader {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,0.75) url("{{asset('assets/img/triangles_indicator.gif')}}") no-repeat center center;
    z-index: 99999;
}
/* To prevent dropdowns from being cut off in table-responsive */
.table-responsive, 
.dataTables_scrollBody,
.tab-content,
.tab-pane {
    overflow: visible !important;
}
.dropdown-menu {
    z-index: 9999 !important;
}
.dropdown {
    position: relative !important;
}

.registration-form .reg-section-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    height: 100%;
}

.registration-form .reg-section-title {
    font-weight: 700;
    color: #334155;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #6366f1;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.registration-form .form-group label {
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.registration-form .form-control,
.registration-form .select2-container .select2-selection--single {
    min-height: 48px;
    font-size: 1rem;
    border-radius: 8px;
    border-color: #cbd5e1;
}

.registration-form .form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.registration-form .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 46px;
    padding-left: 12px;
}

.registration-form .select2-container .select2-selection--single .select2-selection__arrow {
    height: 46px;
}

.registration-form .package-item {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.registration-form .package-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}

.registration-form .package-item-title {
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    font-size: 1rem;
}

.registration-form .receipt-options {
    display: flex;
    gap: 1.5rem;
    padding-top: 0.5rem;
}

.registration-form .receipt-options .form-check {
    margin: 0;
    padding-left: 1.75rem;
}

.registration-form .receipt-options .form-check-label {
    font-weight: 500;
    font-size: 1rem;
}

.registration-form .form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
    margin-top: 0.5rem;
}

.registration-form .help-text {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 0.35rem;
}
</style>

<script src="{{asset('global_assets/js/plugins/forms/inputs/inputmask.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/selects/bootstrap_multiselect.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/uploaders/bs_custom_file_input.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/extensions/jquery_ui/core.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tagsinput.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/tags/tokenfield.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/touchspin.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/maxlength.min.js')}}"></script>
<script src="{{asset('global_assets/js/plugins/forms/inputs/formatter.min.js')}}"></script>
<script src="{{asset('global_assets/js/demo_pages/form_floating_labels.js')}}"></script>
@endpush


@section('content')

<div id='loader'></div>


    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Utambulisho </h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="list-tab2"
                                        data-toggle="tab" href="#list2" role="tab" aria-controls="list"
                                        aria-selected="true">List ya Wateja na Mizigo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="home-tab2"
                                        data-toggle="tab" href="#home2" role="tab" aria-controls="home"
                                        aria-selected="false">Sajili Wateja na Mizigo
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content tab-bordered" id="myTab3Content">
                                <!-- List Tab -->
                                <div class="tab-pane fade show active" id="list2" role="tabpanel" aria-labelledby="list-tab2">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>List ya Wateja na Mizigo</h5>
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
                                            <div class="table-responsive">
                                                <table class="table table-striped datatable-basic">
                                                    <thead>
                                                        <tr>
                                                            <th>Ref No</th>
                                                            <th>Date</th>
                                                            <th>Mpokeaji (Ofisi)</th>
                                                            <th>Namba ya Mpokeaji</th>
                                                            <th>Mteja</th>
                                                            <th>Namba ya Mteja</th>
                                                            <th>Registered By</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($customer_list as $customer)
                                                        <tr>
                                                            <td><a href="{{ route('list_ya_wateja.detail', $customer->id) }}">{{ $customer->unique_identifier }}</a></td>
                                                            <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                                                            <td>{{ $customer->mpokeaji }}</td>
                                                            <td>{{ $customer->receiver_phone }}</td>
                                                            <td>{{ $customer->mteja }}</td>
                                                            <td>{{ $customer->customer_phone }}</td>
                                                            <td>{{ $customer->registeredBy->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <div class="list-icons">
                                                                    <a href="{{ route('customer.pacel.edit', $customer->id) }}" title="Edit" class="list-icons-item text-primary"><i class="icon-pencil7"></i></a>
                                                                    <div class="dropdown">
                                                                        <a href="#" class="list-icons-item text-teal dropdown-toggle my-dropdown-toggle">
                                                                            <i class="icon-cog6"></i>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-right" style="display: none;">
                                                                            <a href="{{ route('list_ya_wateja.editmzigo', $customer->id) }}" class="dropdown-item">
                                                                                <i class="icon-plus2 mr-2"></i> Ongeza Mzigo
                                                                            </a>
                                                                            <a href="{{ route('pacel_reg', $customer->id) }}" target="_blank" rel="opener" class="dropdown-item">
                                                                                <i class="icon-printer mr-2"></i> Print Delivery Note
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Registration Form Tab -->
                                <div class="tab-pane fade @if (!empty($id)) active show @endif"
                                    id="home2" role="tabpanel" aria-labelledby="home-tab2">

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Usajili wa Mteja</h5>
                                        </div>
                                        <div class="card-body registration-form">
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if($cargo_routes->isEmpty())
                                                <div class="alert alert-warning">
                                                    Hakuna routes bado. <a href="{{ route('cargo_routes.index') }}">Tengeneza routes kwanza</a> (kutoka mikoa yako) kisha urudi kusajili mizigo.
                                                </div>
                                            @endif

                                            {{ Form::open(['route' => 'dashboard.store']) }}
                                            @method('POST')

                                            <div class="row mb-4">
                                                <div class="col-lg-6 mb-3 mb-lg-0">
                                                    <div class="reg-section-card">
                                                        <h6 class="reg-section-title">Taarifa za Mpokeaji (Ofisi)</h6>
                                                        <div class="form-group">
                                                            <label for="mpokeaji">Jina La Mpokeaji</label>
                                                            <input type="text" name="mpokeaji" id="mpokeaji"
                                                                class="form-control form-control-lg"
                                                                placeholder="Jina la mtu wa ofisi anayepokea mzigo" required
                                                                value="{{ old('mpokeaji') }}" />
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <label for="receiver_phone">Namba ya Mpokeaji</label>
                                                            <input type="text" name="receiver_phone" id="receiver_phone"
                                                                class="form-control form-control-lg"
                                                                placeholder="Namba ya simu ya ofisi / mpokeaji"
                                                                value="{{ old('receiver_phone') }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="reg-section-card">
                                                        <h6 class="reg-section-title">Taarifa za Mteja</h6>
                                                        <div class="form-group">
                                                            <label for="mteja">Jina La Mteja</label>
                                                            <input type="text" name="mteja" id="mteja"
                                                                class="form-control form-control-lg"
                                                                placeholder="Andika jina la mteja" required
                                                                value="{{ old('mteja') }}" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="customer_phone">Namba ya Mteja</label>
                                                            <input type="text" name="customer_phone" id="customer_phone"
                                                                class="form-control form-control-lg"
                                                                placeholder="Andika namba ya simu ya mteja"
                                                                value="{{ old('customer_phone') }}" />
                                                        </div>
                                                        <div class="form-group mb-0">
                                                            <label for="unique_identifier">Kitambulisho cha Kipekee</label>
                                                            <select name="unique_identifier" id="unique_identifier"
                                                                class="form-control form-control-lg" required>
                                                                <option value="{{ $nextIdentifier }}" selected>{{ $nextIdentifier }}</option>
                                                            </select>
                                                            <div class="help-text">
                                                                Kitambulisho cha kipekee cha kesi hii (RG-001, RG-002, RG-003, n.k.)
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(isset($customer_ID))
                                                <div class="text-center mb-4">
                                                    <a id="auto-print-receipt-link" class="btn btn-primary btn-lg" href="{{ route('pacel_reg', $customer_ID) }}" target="_blank" rel="opener" role="button">
                                                        <i class="icon-printer mr-1"></i> Print Delivery Note
                                                    </a>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <h6 class="font-weight-bold text-slate-700 mb-0">Maelezo ya Mizigo</h6>
                                                <small class="text-muted">Bei ni hiari. Unaweza kuacha bei wazi ikiwa haiko.</small>
                                            </div>

                                            <div id="package_container">
                                                <div class="package-item" data-package-index="0">
                                                    <div class="row">
                                                        <div class="col-12 form-group">
                                                            <label>Jina la Mzigo</label>
                                                            <input type="text" name="name[]" class="form-control form-control-lg"
                                                                placeholder="Andika jina la mzigo" required />
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Idadi ya Mzigo</label>
                                                            <input type="number" name="quantity[]" step="any"
                                                                class="form-control form-control-lg item_quantity"
                                                                placeholder="Idadi" required />
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Bei ya kila mzigo <span class="text-muted">(si lazima)</span></label>
                                                            <input type="number" name="price[]" step="any"
                                                                class="form-control form-control-lg item_price"
                                                                placeholder="Bei (si lazima)" value="" />
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Jumla Kuu</label>
                                                            <input type="text" name="total_cost[]"
                                                                class="form-control form-control-lg item_total bg-light"
                                                                placeholder="Jumla" readonly />
                                                        </div>
                                                        <div class="col-md-4 form-group">
                                                            <label>Aina ya Risiti</label>
                                                            <div class="receipt-options">
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input" name="receipt[0]" value="R" checked>
                                                                    <label class="form-check-label">R</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="radio" class="form-check-input" name="receipt[0]" value="HR">
                                                                    <label class="form-check-label">HR</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                        @include('partials.cargo_route_select', [
                                                            'cargo_routes' => $cargo_routes,
                                                            'disabled' => $cargo_routes->isEmpty(),
                                                            'required' => true,
                                                        ])
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <small class="text-muted">Hakikisha taarifa zote zimejazwa kabla ya kuhifadhi.</small>
                                                <button class="btn btn-primary btn-lg px-5" type="submit" id="save">
                                                    <i class="icon-checkmark3 mr-1"></i> Hifadhi
                                                </button>
                                            </div>

                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- supplier Modal -->
    <div class="modal fade " data-backdrop="" id="appFormModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-lg">

        </div>
    </div>


    <div class="modal fade " data-backdrop="" id="app2FormModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">

        </div>
    </div>




    <script>
        $(document).ready(function() {
            // Handle custom dropdown toggle
            $('.my-dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var $menu = $(this).next('.dropdown-menu');
                var isVisible = $menu.is(':visible');

                // Close all other dropdowns
                $('.dropdown-menu').hide();

                // Toggle this one
                if (!isVisible) {
                    $menu.show();
                    // Optional: adjust position if needed
                    $menu.css({
                        'position': 'absolute',
                        'top': $(this).outerHeight() + 'px',
                        'right': '0',
                        'left': 'auto',
                        'z-index': '9999'
                    });
                }
            });

            // Close dropdowns when clicking anywhere else
            $(document).on('click', function() {
                $('.dropdown-menu').hide();
            });

            // Prevent dropdown closure when clicking inside the menu
            $('.dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
@endsection

@push('plugin-scripts')

<script>
$(function() {
    $( "form" ).submit(function() {
        $('#loader').show();
    });
});
</script>


{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@section('scripts')
    <script>
        $(document).ready(function() {
            // Clone first package - button lives at bottom of each package
            $(document).on('click', '.add_more_package', function() {
                var $container = $('#package_container');
                var $first = $container.find('.package-item').first();
                if (!$first.length) {
                    return;
                }

                var nextIndex = $container.find('.package-item').length;
                var $clone = $first.clone(false);

                $clone.find('input[type="text"], input[type="number"]').val('');
                $clone.find('input.item_total').val('');
                $clone.find('.cargo-split-rows').empty();
                $clone.find('.cargo-split-destinations-wrap').hide();
                $clone.find('.cargo-split-sum').text('0');
                $clone.find('.cargo-split-target').text('0');
                $clone.find('.cargo-split-status').text('');
                $clone.find('.cargo-split-error').addClass('d-none').text('');
                $clone.removeClass('cargo-split-invalid cargo-split-valid');

                $clone.find('.select2-container').remove();
                $clone.find('select.route-select')
                    .removeClass('select2-hidden-accessible')
                    .removeAttr('data-select2-id tabindex aria-hidden')
                    .val('');
                $clone.find('select.route-select option').removeAttr('data-select2-id');

                $clone.find('.package-item-title').text('Mzigo #' + (nextIndex + 1));
                if (!$clone.find('.remove_package').length) {
                    $clone.find('.package-item-header').append(
                        '<button type="button" class="btn btn-outline-danger btn-sm remove_package">' +
                        '<i class="icon-trash mr-1"></i> Ondoa</button>'
                    );
                }

                // Ensure footer add button exists on clone
                if (!$clone.find('.add_more_package').length) {
                    $clone.append(
                        '<div class="package-item-footer mt-3 pt-3 border-top text-right">' +
                        '<button type="button" class="btn btn-success add_more_package">' +
                        '<i class="icon-plus2 mr-1"></i> Ongeza Mzigo</button></div>'
                    );
                }

                $clone.find('input[type="radio"][name^="receipt"]').each(function() {
                    var $radio = $(this);
                    var val = $radio.val();
                    var id = 'receipt_' + nextIndex + '_' + val;
                    $radio.attr('name', 'receipt[' + nextIndex + ']');
                    $radio.attr('id', id);
                    $radio.prop('checked', val === 'R');
                    $radio.next('label').attr('for', id);
                });

                $clone.attr('data-package-index', nextIndex);
                $container.append($clone);

                if (typeof window.initCargoRouteSelect === 'function') {
                    window.initCargoRouteSelect($clone);
                }
                if (typeof window.reindexPackageSplits === 'function') {
                    window.reindexPackageSplits($container);
                }

                $('html, body').animate({ scrollTop: $clone.offset().top - 100 }, 350);
            });

            $(document).on('click', '.remove_package', function() {
                var $container = $('#package_container');
                $(this).closest('.package-item').remove();
                $container.find('.package-item').each(function(i) {
                    $(this).find('.package-item-title').text('Mzigo #' + (i + 1));
                    $(this).find('input[type="radio"][name^="receipt"]').each(function() {
                        var $radio = $(this);
                        var val = $radio.val();
                        var id = 'receipt_' + i + '_' + val;
                        $radio.attr('name', 'receipt[' + i + ']');
                        $radio.attr('id', id);
                        $radio.next('label').attr('for', id);
                    });
                });
                if (typeof window.reindexPackageSplits === 'function') {
                    window.reindexPackageSplits($container);
                }
            });

            // Auto-calculation: quantity × price (price optional → 0)
            $(document).on('input change keyup', '.item_quantity, .item_price', function() {
                var $container = $(this).closest('.package-item');
                var qty = parseFloat($container.find('.item_quantity').val()) || 0;
                var price = parseFloat($container.find('.item_price').val()) || 0;
                var total = qty * price;
                $container.find('.item_total').val(total.toFixed(2));
            });

            // Initial Select2
            // Standard select2 for non-route fields only
            $('.select-search').not('.route-select').select2();

            // Initialize flatpickr
            $(".flatpickr").flatpickr({
                dateFormat: "Y-m-d",
            });

            $('#filter_button').on('click', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                
                var url = new URL(window.location.href);
                url.searchParams.set('start_date', startDate);
                url.searchParams.set('end_date', endDate);
                
                window.location.href = url.toString();
            });
        });
    </script>
    <script>
        $('.datatable-basic').DataTable({
            autoWidth: false,
            order: [
                [1, 'desc']
            ],
            "columnDefs": [{
                "targets": [7],
                "orderable": false
            }],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            "language": {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: {
                    'first': 'First',
                    'last': 'Last',
                    'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;',
                    'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;'
                }
            },

        });
    </script>




    <script>
        $(document).ready(function() {

            $(document).on('click', '.remove', function() {
                $(this).closest('tr').remove();
            });

        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            var count = 0;


            function autoCalcSetup() {
                $('table#cart').jAutoCalc('destroy');
                $('table#cart tr').jAutoCalc({
                    keyEventsFire: true,
                    decimalPlaces: 2,
                    emptyAsZero: true
                });
                $('table#cart').jAutoCalc({
                    decimalPlaces: 2
                });
            }
            autoCalcSetup();


        });
    </script>




    @include('partials.cargo_route_select_script', [
        'cargo_routes' => $cargo_routes,
        'cargo_route_stops' => $cargo_route_stops ?? [],
    ])

    @if(isset($customer_ID))
    <script>
        (function () {
            var link = document.getElementById('auto-print-receipt-link');
            if (!link) return;
            // Prefer click() over window.open - POS browsers often block popups.
            setTimeout(function () {
                try {
                    link.setAttribute('rel', 'opener');
                    link.click();
                } catch (e) {
                    window.open(link.href, '_blank');
                }
            }, 500);
        })();
    </script>
    @endif
    
@endsection
