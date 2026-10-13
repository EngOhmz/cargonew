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
                        <h4>Matumizi ya Route</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="h6">
                                <strong>Gari:</strong> {{ $route->car ? $route->car->carNumber : '-' }}<br>
                                <strong>Dereva:</strong> {{ $route->driver ? $route->driver->name : 'N/A' }}<br>
                                <strong>Njia:</strong> {{ $route->from }} &rarr; {{ $route->to }}<br>
                                <strong>Tarehe ya Kuanza:</strong> {{ $route->start_date }}<br>
                                <strong>Tarehe ya Kufunga:</strong> {{ $route->closeDate ?: '-' }}
                            </p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-5">
                                <h6>Ongeza Matumizi</h6>
                                <form method="POST" action="{{ route('route_expenses.store') }}">
                                    @csrf
                                    <input type="hidden" name="driver_route_id" value="{{ $route->id }}">
                                    <input type="hidden" name="car_id" value="{{ $route->car_id }}">
                                    <input type="hidden" name="driver_id" value="{{ $route->driver_id }}">

                                    <div class="form-group">
                                        <label for="expense_date">Tarehe</label>
                                        <input type="date" id="expense_date" name="expense_date"
                                               class="form-control"
                                               value="{{ old('expense_date', now()->toDateString()) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="category">Aina ya Gharama (mf. Mafuta, Tolls, Matengenezo)</label>
                                        <input type="text" id="category" name="category"
                                               class="form-control"
                                               value="{{ old('category') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Maelezo</label>
                                        <textarea id="description" name="description" rows="3"
                                                  class="form-control">{{ old('description') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="amount">Kiasi (TZS)</label>
                                        <input type="number" step="0.01" min="0" id="amount" name="amount"
                                               class="form-control"
                                               value="{{ old('amount') }}" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Hifadhi Matumizi</button>
                                </form>
                            </div>

                            <div class="col-md-7">
                                <h6>Orodha ya Matumizi ya Route Hii</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tarehe</th>
                                                <th>Aina</th>
                                                <th>Maelezo</th>
                                                <th>Kiasi (TZS)</th>
                                                <th>Mwandishi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($expenses as $exp)
                                                <tr>
                                                    <td>{{ $exp->expense_date }}</td>
                                                    <td>{{ $exp->category ?? '-' }}</td>
                                                    <td>{{ $exp->description ?? '-' }}</td>
                                                    <td>{{ number_format($exp->amount, 2) }}</td>
                                                    <td>{{ optional($exp->creator)->name ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Hakuna matumizi yaliyorekodiwa kwa route hii.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-right">Jumla ya Matumizi:</th>
                                                <th colspan="2">{{ number_format($totalAmount, 2) }} TSH</th>
                                            </tr>
                                        </tfoot>
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

@push('plugin-scripts')
{!! Html::script('assets/js/loader.js') !!}
{!! Html::script('plugins/apex/apexcharts.min.js') !!}
@endpush

@push('custom-scripts')
@endpush

