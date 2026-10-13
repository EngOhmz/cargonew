@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
{!! Html::style('plugins/flatpickr/flatpickr.css') !!}
{!! Html::style('plugins/flatpickr/custom-flatpickr.css') !!}
{!! Html::style('assets/css/elements/tooltip.css') !!}

<style>
    .data-table .store-row-actions { position: static; }
    .data-table .store-row-actions .dropdown-menu {
        position: fixed !important;
        z-index: 1050 !important;
        min-width: 11rem;
    }
    .data-table .store-action-toggle {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .table-responsive { overflow-x: auto; overflow-y: visible; }
    .data-table td:last-child,
    .data-table th:last-child { white-space: nowrap; width: 1%; }
</style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-1">Mizigo Bado Stoo</h4>
                        <p class="text-muted mb-0 small">
                            Inaonyesha mizigo iliyobaki stoo tu (haijapakiwa). Ikishapakiwa kikamilifu, mzigo unaondoka hapa.
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>REF No#</th>
                                        <th>Mpokeaji (Ofisi)</th>
                                        <th>Tarehe</th>
                                        <th>CARGO</th>
                                        <th>BADO STOO</th>
                                        <th>@PRICE</th>
                                        <th>THAMANI STOO</th>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>TOTAL PAID</th>
                                        <th>R</th>
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

@include('management.partials.pacel_store_modals')
@endsection

@push('plugin-scripts')
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
{!! Html::script('plugins/flatpickr/flatpickr.js') !!}
{!! Html::script('assets/js/dashboard/dashboard_1.js') !!}
@endpush

@push('custom-scripts')
@include('management.partials.pacel_store_scripts', ['dataRoute' => route('pacel_store'), 'isDebtPage' => false])
@endpush
