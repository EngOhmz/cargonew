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
        width: 32px; height: 32px; padding: 0;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%;
    }
    .table-responsive { overflow-x: auto; overflow-y: visible; }
    .data-table td:last-child, .data-table th:last-child { white-space: nowrap; width: 1%; }
    .debt-sidebar .sidebar-filter { cursor: pointer; }
    .debt-sidebar .sidebar-filter.active {
        background: #2D335E !important;
        border-color: #2D335E !important;
        color: #fff !important;
    }
    .debt-sidebar .sidebar-filter.active .text-slate-800 { color: #fff !important; }
    .debts-page-header {
        background: linear-gradient(135deg, #2D335E 0%, #3d4a8f 100%);
        border-radius: 12px;
        color: #fff;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
    }
    .debts-page-header h4 { color: #fff; font-weight: 700; margin-bottom: 0.35rem; }
    .debts-page-header p { color: rgba(255,255,255,0.85); margin-bottom: 0; }
    .debts-stat-pill {
        display: inline-block;
        background: rgba(255,255,255,0.15);
        border-radius: 999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.82rem;
        margin-right: 0.5rem;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="debts-page-header">
            <h4><i class="icon-coin-dollar mr-2"></i>Madeni ya Mizigo</h4>
            <p>Mizigo yote yenye deni (haijalipwa kikamilifu). Malipo kutoka app au ofisi yanaongezwa hapa; deni linalobaki linaendelea kuonekana.</p>
            <div>
                <span class="debts-stat-pill">Mizigo: <strong>{{ $dueParcelsCount ?? 0 }}</strong></span>
                <span class="debts-stat-pill">Wateja: <strong>{{ $dueCustomersCount ?? 0 }}</strong></span>
                <span class="debts-stat-pill">Deni jumla: <strong>{{ number_format($totalDue ?? 0, 0, '.', ',') }} TZS</strong></span>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="font-weight-bold mb-3">Wateja wenye deni</h5>
                        <div class="list-group debt-sidebar">
                            <a href="#" class="list-group-item list-group-item-action sidebar-filter active" data-mpokeaji="">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold text-slate-800">Wateja wote wenye deni</span>
                                    <span class="badge badge-primary">{{ $dueParcelsCount ?? 0 }}</span>
                                </div>
                            </a>
                            @if(isset($customersDue) && $customersDue->count() > 0)
                                @foreach($customersDue as $c)
                                    <a href="#" class="list-group-item list-group-item-action sidebar-filter" data-mpokeaji="{{ $c['mpokeaji'] }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="font-weight-bold text-slate-800">{{ $c['mpokeaji'] }}</span>
                                            <span class="badge badge-info">{{ number_format($c['total_due'] ?? 0, 0, '.', ',') }}</span>
                                        </div>
                                        <div class="mt-2">
                                            @if(($c['unpaid_count'] ?? 0) > 0)
                                                <span class="badge badge-light border mr-1">Haijalipwa: {{ $c['unpaid_count'] }}</span>
                                            @endif
                                            @if(($c['partial_count'] ?? 0) > 0)
                                                <span class="badge badge-warning mr-1">Partial: {{ $c['partial_count'] }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="alert alert-success mb-0">Hakuna deni kwa kampuni yako kwa sasa.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>REF No#</th>
                                        <th>Mpokeaji (Ofisi)</th>
                                        <th>Tarehe</th>
                                        <th>CARGO</th>
                                        <th>QNT</th>
                                        <th>@PRICE</th>
                                        <th>TOTAL</th>
                                        <th>IMEPOKELEWA</th>
                                        <th>BAKI (DENI)</th>
                                        <th>FROM</th>
                                        <th>TO</th>
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
@include('management.partials.pacel_store_scripts', ['dataRoute' => route('outstanding_debts'), 'isDebtPage' => true])
@endpush
