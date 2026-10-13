@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/apex/apexcharts.css') !!}
{!! Html::style('assets/css/dashboard/dashboard_1.css') !!}
<style>
    .routes-overview-scroll {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        overflow-y: visible;
        -webkit-overflow-scrolling: touch;
    }
    .routes-overview-scroll .table-responsive {
        overflow: visible;
        margin-bottom: 0;
    }
    .routes-overview-scroll .table-responsive .table {
        min-width: max-content;
        width: max-content;
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
                        <h4>Njia na Sehemu/Vituo vya kushushia mizigo</h4>
                    </div>
                    <div class="card-body">
                        <div class="routes-overview-scroll">
                        @if($routes->isEmpty())
                            <p class="text-muted">Hakuna njia zilizopatikana kwa kampuni hii.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Gari</th>
                                            <th>Dereva</th>
                                            <th>Inapotoka</th>
                                            <th>Inapokwenda</th>
                                            <th>Tarehe ya Kufunga</th>
                                            <th>Hali</th>
                                            <th>Vituo vilivyoshushwa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($routes as $index => $route)
                                            @php
                                                $routeDrops = $dropsByRoute[$route->id] ?? [];
                                                $isClosed = !empty($route->closeDate) && $route->closeDate !== '0000-00-00';
                                                $routeStatus = $isClosed ? 'Imefungwa' : 'Ipo Wazi';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $route->car_name ?? '-' }}</td>
                                                <td>{{ $route->driver_name ?? '-' }}</td>
                                                <td>{{ $route->from }}</td>
                                                <td>{{ $route->to }}</td>
                                                <td>{{ $route->closeDate ?: '-' }}</td>
                                                <td>
                                                    <span class="badge {{ $isClosed ? 'badge-danger' : 'badge-success' }}">
                                                        {{ $routeStatus }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(empty($routeDrops))
                                                        <span class="text-muted">Hakuna vituo vilivyoshushwa</span>
                                                    @else
                                                        <table class="table table-sm mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Kituo/Sehemu iliyoshushwa</th>
                                                                    <th>Dereva</th>
                                                                    <th>Gari</th>
                                                                    <th>Idadi Iliyoshushwa</th>
                                                                    <th>Ela Iliyopokelewa</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($routeDrops as $dp)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="font-bold">{{ $dp['drop_off_point'] }}</span>
                                                                            @if($dp['has_drop'] && count($dp['parcels']) > 0)
                                                                                <div class="mt-1">
                                                                                    <small class="text-muted block mb-1">Mizigo iliyoshushwa:</small>
                                                                                    @foreach($dp['parcels'] as $parcel)
                                                                                        <div class="mb-1 p-1 bg-light rounded border-left border-info" style="font-size: 0.75rem;">
                                                                                            <a href="{{ route('list_ya_mizigo.show', $parcel['id']) }}" class="text-primary font-weight-bold">
                                                                                                RG-{{ $parcel['hashtag'] }}
                                                                                            </a>: 
                                                                                            {{ $parcel['name'] }} 
                                                                                            <span class="badge badge-pill badge-info ml-1">{{ $parcel['dropped'] }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ $dp['driver_name'] ?? '-' }}</td>
                                                                        <td>{{ $dp['car_name'] ?? '-' }}</td>
                                                                        <td>
                                                                            @if($dp['has_drop'])
                                                                                <span class="badge badge-info">
                                                                                    {{ $dp['total_dropped'] }} (Ilishushwa)
                                                                                </span>
                                                                            @else
                                                                                <span class="badge badge-secondary">
                                                                                    0 (Haijashushwa)
                                                                                </span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if($dp['has_drop'])
                                                                                {{ number_format($dp['total_received'], 2) }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
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


