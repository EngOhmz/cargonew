@extends('layout.master')

@push('plugin-styles')
<script src="{{ asset('global_assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<style>
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

.registration-form .package-item.readonly-item {
    background: #f8fafc;
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

.registration-form .total-summary {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 10px;
    padding: 1rem 1.25rem;
    max-width: 320px;
    margin-left: auto;
    margin-bottom: 1rem;
}

.registration-form .total-summary label {
    margin-bottom: 0.35rem;
    color: #4338ca;
}

.registration-form {
    padding-bottom: 5.5rem;
}

.editmzigo-sticky-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1040;
    background: #fff;
    border-top: 1px solid #e2e8f0;
    box-shadow: 0 -4px 16px rgba(15, 23, 42, 0.1);
    padding: 0.85rem 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.editmzigo-sticky-bar.border-danger {
    border-top-color: #dc2626 !important;
    box-shadow: 0 -4px 16px rgba(220, 38, 38, 0.2);
}

@media (min-width: 992px) {
    .editmzigo-sticky-bar {
        left: 16.875rem;
    }

    .sidebar-xs .editmzigo-sticky-bar,
    .sidebar-main-hidden .editmzigo-sticky-bar {
        left: 0;
    }
}
</style>
{!! Html::style('assets/css/cargo-route-select.css') !!}
@endpush

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Kuongeza Mizigo</h4>
                            <a href="{{ route('list_ya_wateja.detail', $data2->id) }}" class="btn btn-light btn-sm">
                                <i class="icon-arrow-left13 mr-1"></i> Rudi
                            </a>
                        </div>
                        <div class="card-body registration-form">
                            {{ Form::open(['route' => 'mizigo.kuongeza']) }}
                            @method('POST')
                            <input type="hidden" name="c_id" value="{{ $data2->id }}">

                            @if($cargo_routes->isEmpty())
                                <div class="alert alert-warning">
                                    Hakuna routes bado. <a href="{{ route('cargo_routes.index') }}">Tengeneza routes kwanza</a> ili uweze kuongeza mizigo mpya.
                                </div>
                            @endif
                            @if ($errors->has('drop_split'))
                                <div class="alert alert-danger">
                                    {{ $errors->first('drop_split') }}
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-lg-6 mb-3 mb-lg-0">
                                    <div class="reg-section-card">
                                        <h6 class="reg-section-title">Taarifa za Mpokeaji (Ofisi)</h6>
                                        <div class="form-group mb-0">
                                            <label>Jina La Mpokeaji</label>
                                            <input type="text" class="form-control form-control-lg bg-light"
                                                value="{{ $data2->mpokeaji }}" readonly required />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="reg-section-card">
                                        <h6 class="reg-section-title">Taarifa za Mteja</h6>
                                        <div class="form-group mb-0">
                                            <label>Jina La Mteja</label>
                                            <input type="text" class="form-control form-control-lg bg-light"
                                                value="{{ $data2->mteja }}" readonly required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(isset($customer_ID))
                                <div class="text-center mb-4">
                                    <a class="btn btn-primary btn-lg" href="{{ route('pacel_reg', $customer_ID) }}" target="_blank" rel="opener" role="button">
                                        <i class="icon-printer mr-1"></i> Print Delivery Note
                                    </a>
                                </div>
                            @endif

                            @if (!empty($items) && $items->count())
                                <h6 class="font-weight-bold text-slate-700 mb-3">Mizigo Iliyopo</h6>
                                @foreach ($items as $index => $i)
                                    <div class="package-item readonly-item">
                                        <div class="package-item-header">
                                            <h6 class="package-item-title">Mzigo #{{ $index + 1 }}</h6>
                                            <span class="badge badge-light">{{ $i->receipt }}</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Mteja / Supplier</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->mteja }}" readonly />
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Mpokeaji</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->mpokeaji }}" readonly />
                                            </div>
                                            <div class="col-12 form-group">
                                                <label>Jina la Mzigo</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->name }}" readonly />
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Idadi ya Mzigo</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->idadi_stoo }}" readonly />
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Bei ya kila mzigo</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->bei }}" readonly />
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Jumla Kuu</label>
                                                <input type="text" class="form-control form-control-lg bg-light"
                                                    value="{{ $i->jumla }}" readonly />
                                            </div>
                                            <div class="col-12 form-group mb-0">
                                                <label class="cargo-route-label">
                                                    Mahali pa Kushusha
                                                </label>
                                                <div class="cargo-route-readonly">
                                                    <span class="cargo-route-pill">
                                                        <span class="cargo-route-from">{{ $i->mzigo_unapotoka }}</span>
                                                        <span class="cargo-route-arrow" aria-hidden="true"><i class="icon-arrow-right13"></i></span>
                                                        <span class="cargo-route-to">{{ $i->mzigo_unapokwenda }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <h6 class="font-weight-bold text-slate-700 mb-3 mt-4">Ongeza Mizigo Mpya</h6>
                            <p class="text-muted small mb-3">
                                Mpokeaji (ofisi) ni <strong>{{ $data2->mpokeaji }}</strong> - haubadiliki hapa.
                                Ongeza mizigo kutoka mtumaji mwingine; mtumaji wa mstari mpya unachukuliwa kutoka juu (au mtumaji wa kesi).
                            </p>

                            <div id="new_package_container"></div>

                            <div class="total-summary">
                                <label class="font-weight-bold mb-0">Jumla ya Mizigo Mpya</label>
                                <input type="text" name="subtotal[]" class="form-control form-control-lg bg-white font-weight-bold mt-2" readonly value="0.00">
                            </div>

                            <div class="editmzigo-sticky-bar" id="editmzigo_sticky_bar">
                                <div class="cargo-split-form-alert alert alert-danger mb-0 py-2 px-3 d-none" style="flex: 1 1 100%;"></div>
                                <small class="text-muted d-none d-md-inline">Ongeza mizigo zaidi au hifadhi mabadiliko.</small>
                                <div class="d-flex ml-md-auto" style="gap: 0.75rem;">
                                    <button type="button" name="add" class="btn btn-success add">
                                        <i class="icon-plus2 mr-1"></i> Ongeza Mzigo
                                    </button>
                                    <button class="btn btn-primary px-4" type="submit" id="save">
                                        <i class="icon-checkmark3 mr-1"></i> Hifadhi
                                    </button>
                                </div>
                            </div>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var packageCount = 0;
            var defaultMtumaji = @json($data2->mteja ?? '');

            function escapeAttr(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            function supplierForNextLine() {
                var $lines = $('#new_package_container .line-mteja-input');
                if ($lines.length) {
                    var last = String($lines.last().val() || '').trim();
                    if (last !== '') {
                        return last;
                    }
                }
                return String(defaultMtumaji || '').trim();
            }

            function recalcRow($container) {
                var q = parseFloat($container.find('input[name="quantity[]"]').val()) || 0;
                var p = parseFloat($container.find('input[name="price[]"]').val()) || 0;
                $container.find('input[name="total_cost[]"]').val((q * p).toFixed(2));
            }

            function recalcFooter() {
                var sum = 0;
                $('input[name="total_cost[]"]').each(function() {
                    sum += parseFloat($(this).val()) || 0;
                });
                $('input[name="subtotal[]"]').val(sum.toFixed(2));
            }

            function debounce(fn, delay) {
                var t;
                return function() {
                    var ctx = this, args = arguments;
                    clearTimeout(t);
                    t = setTimeout(function() { fn.apply(ctx, args); }, delay);
                };
            }

            function initSelect2($scope) {
                $scope.find('.select-search').not('.route-select').select2();
                if (typeof window.initCargoRouteSelect === 'function') {
                    window.initCargoRouteSelect($scope);
                }
            }

            $('.add').on('click', function() {
                packageCount++;
                var lineMtumaji = supplierForNextLine();
                var html = `
                    <div class="package-item new-package-item">
                        <div class="package-item-header">
                            <h6 class="package-item-title">Mzigo Mpya #${packageCount}</h6>
                            <button type="button" class="btn btn-outline-danger btn-sm remove">
                                <i class="icon-trash mr-1"></i> Ondoa
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-12 form-group">
                                <label>Mteja / Supplier <small class="text-muted">- kwa mzigo huu</small></label>
                                <input type="text" name="line_mteja[]" class="form-control form-control-lg line-mteja-input"
                                    placeholder="Mfano: Supplier B, Duka la Juma"
                                    value="${escapeAttr(lineMtumaji)}" required />
                            </div>
                            <div class="col-12 form-group">
                                <label>Jina la Mzigo</label>
                                <input type="text" name="name[]" class="form-control form-control-lg" placeholder="Andika jina la mzigo" required />
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Idadi ya Mzigo</label>
                                <input type="number" name="quantity[]" step="any" class="form-control form-control-lg item_quantity" placeholder="Idadi" required />
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Bei ya kila mzigo <span class="text-muted">(si lazima)</span></label>
                                <input type="number" name="price[]" step="any" class="form-control form-control-lg item_price" placeholder="Bei (si lazima)" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Jumla Kuu</label>
                                <input type="text" name="total_cost[]" class="form-control form-control-lg item_total bg-light" placeholder="Jumla" readonly />
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Aina ya Risiti</label>
                                <select name="receipt[]" class="form-control form-control-lg" required>
                                    <option value="">Chagua</option>
                                    <option value="R">R</option>
                                    <option value="HR">HR</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="cargo-route-field form-group mb-0">
                                    <div class="cargo-route-drop-grid">
                                        <div class="cargo-route-drop-block">
                                            <label class="cargo-route-label">Njia ya Usafirishaji <small class="cargo-route-label-hint">Route kuu</small></label>
                                            <div class="cargo-route-select-wrap">
                                                <select name="route_id[]" class="form-control form-control-lg route-select cargo-main-route" required>
                                                    ${$('#cargo-route-options-html').html()}
                                                </select>
                                            </div>
                                        </div>
                                        ${$('#cargo-route-split-wrap-html').html()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                var $newPackage = $(html);
                $('#new_package_container').append($newPackage);
                initSelect2($newPackage);
                recalcRow($newPackage);
                recalcFooter();

                $('html, body').animate({
                    scrollTop: $newPackage.offset().top - 100
                }, 300);
            });

            $(document).on('click', '.remove', function() {
                $(this).closest('.new-package-item').remove();
                if (typeof window.reindexPackageSplits === 'function') {
                    window.reindexPackageSplits($('#new_package_container'));
                }
                recalcFooter();
            });

            $(document).on('input', 'input[name="quantity[]"], input[name="price[]"]', debounce(function() {
                var $container = $(this).closest('.new-package-item');
                recalcRow($container);
                recalcFooter();
            }, 120));
        });
    </script>

    @include('partials.cargo_route_select_script', [
        'cargo_routes' => $cargo_routes,
        'cargo_route_stops' => $cargo_route_stops ?? [],
    ])
@endsection
