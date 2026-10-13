@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Ripoti ya Route - {{ $carNumber }}</h4>
                        <p class="mb-0 text-muted">{{ $dt }} - {{ $dt34 }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="text-white-50">Mapato (Kusanywa)</h6>
                                        <h4 class="mb-0">{{ number_format($income, 2) }} TSH</h4>
                                        <small>Jumla ya fedha iliyopokelewa kutoka mizigo</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body">
                                        <h6>Matumizi (Gharama)</h6>
                                        <h4 class="mb-0">{{ number_format($totalExpense, 2) }} TSH</h4>
                                        <small>Jumla ya matumizi ya route</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card {{ $profit >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                    <div class="card-body">
                                        <h6 class="text-white-50">{{ $profit >= 0 ? 'Faida' : 'Hasara' }}</h6>
                                        <h4 class="mb-0">{{ number_format($profit, 2) }} TSH</h4>
                                        <small>{{ $profit >= 0 ? 'Mapato zaidi ya matumizi' : 'Matumizi zaidi ya mapato' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <p class="mb-2">
                                    <strong>Ulinganisho:</strong> Mapato ({{ number_format($income, 2) }} TSH) − Matumizi ({{ number_format($totalExpense, 2) }} TSH) = {{ $profit >= 0 ? 'Faida' : 'Hasara' }} ({{ number_format($profit, 2) }} TSH)
                                </p>
                                <a href="{{ route('car_pacel_detail', [$carId, $dt, $dt34]) }}" class="btn btn-outline-secondary btn-sm">Rudi kwenye mizigo</a>
                                <a href="{{ url('management/car_today_routes/' . $carId . '/show') }}" class="btn btn-outline-secondary btn-sm">Rudi kwenye route</a>
                            </div>
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
@endpush

@push('custom-scripts')
@endpush
