@extends('layout.master')

@push('plugin-styles')
<style>
    .routes-page-header {
        background: linear-gradient(135deg, #2D335E 0%, #3d4a8f 100%);
        border-radius: 12px;
        color: #fff;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
    }
    .routes-page-header h4 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .routes-page-header p {
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0;
        max-width: 52rem;
    }
    .routes-stat-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        height: 100%;
    }
    .routes-stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    .routes-stat-card .stat-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 600;
    }
    .routes-create-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }
    .routes-create-card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-radius: 12px 12px 0 0 !important;
        padding: 1rem 1.25rem;
    }
    .routes-create-card .card-header h6 {
        margin: 0;
        font-weight: 700;
        color: #1e293b;
    }
    .routes-help-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 1rem 1.15rem;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
        color: #1e3a5f;
    }
    .routes-help-box strong {
        color: #1e40af;
    }
    .route-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .route-card-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .route-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .route-card-meta {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.15rem;
    }
    .route-timeline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.35rem 0.5rem;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .route-timeline-node {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .route-timeline-node.from {
        background: #e0f2fe;
        color: #0369a1;
    }
    .route-timeline-node.mid {
        background: #f1f5f9;
        color: #475569;
    }
    .route-timeline-node.final {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .route-timeline-arrow {
        color: #94a3b8;
        font-size: 0.75rem;
    }
    .route-stops-section {
        padding: 1.15rem 1.25rem 1.25rem;
    }
    .route-stops-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.75rem;
    }
    .route-stop-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.65rem 0.85rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .route-stop-row:last-child {
        margin-bottom: 0;
    }
    .route-stop-name {
        font-weight: 600;
        color: #1e293b;
    }
    .route-stop-order {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-left: 0.5rem;
    }
    .route-add-stop {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #cbd5e1;
    }
    .route-add-stop label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
    }
    .routes-empty {
        text-align: center;
        padding: 3rem 1.5rem;
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        color: #64748b;
    }
    .routes-empty i {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
    }
    .route-flow-hint {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.5rem;
    }
    .route-flow-hint i {
        color: #3b82f6;
    }
</style>
@endpush

@section('content')
@php
    $routesCount = $routes->count();
    $mikoaCount = $mikoa->count();
@endphp

<div class="routes-page-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap">
        <div>
            <h4><i class="icon-road mr-2"></i>Usimamizi wa Routes</h4>
            <p>
                Hapa unafafanua <strong>njia za usafirishaji</strong> kwa kampuni yako: kutoka wapi
                na mwisho wa safari. Routes hizi hutumika wakati wa kusajili mizigo, kupakia, na kushusha kwenye app.
            </p>
        </div>
        <a href="{{ route('mikoa.index') }}" class="btn btn-light btn-sm mt-2 mt-md-0">
            <i class="icon-location4 mr-1"></i> Simamia Mikoa
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon-checkmark-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if($errors->has('duplicate') || $errors->has('stop'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first('duplicate') ?: $errors->first('stop') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if($errors->any() && !$errors->has('duplicate') && !$errors->has('stop'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card routes-stat-card">
            <div class="card-body">
                <div class="stat-label">Routes zilizosajiliwa</div>
                <div class="stat-value text-primary">{{ $routesCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card routes-stat-card">
            <div class="card-body">
                <div class="stat-label">Mikoa / maeneo</div>
                <div class="stat-value text-teal">{{ $mikoaCount }}</div>
            </div>
        </div>
    </div>
    @if(config('app.allow_enroute_drops'))
    <div class="col-md-4">
        <div class="card routes-stat-card">
            <div class="card-body">
                <div class="stat-label">Vituo vya njiani (jumla)</div>
                <div class="stat-value text-indigo">
                    {{ $routes->sum(fn ($r) => $r->stops->where('is_final', false)->count()) }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if($mikoa->isEmpty())
    <div class="alert alert-warning border-0 shadow-sm">
        <h6 class="alert-heading font-weight-bold mb-2">
            <i class="icon-warning mr-1"></i> Hakuna mikoa bado
        </h6>
        <p class="mb-2">Kabla ya kutengeneza routes, lazima uwe na orodha ya <strong>mikoa au maeneo</strong> (mfano: Dar es Salaam, Morogoro, Mwanza).</p>
        <a href="{{ route('mikoa.index') }}" class="btn btn-warning btn-sm">Nenda kusajili mikoa</a>
    </div>
@else
    <div class="card routes-create-card">
        <div class="card-header">
            <h6><i class="icon-plus-circle2 mr-1 text-primary"></i> Ongeza Route Mpya</h6>
        </div>
        <div class="card-body">
            <div class="routes-help-box">
                <strong>Route kuu</strong> = mstari wa safari kutoka eneo A hadi eneo B.
                Mizigo yote inashushwa <strong>mwisho wa safari</strong> tu.
            </div>

            <form action="{{ route('cargo_routes.store') }}" method="POST">
                @csrf
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold text-slate-700">
                            <i class="icon-location3 text-info mr-1"></i> Kutoka
                        </label>
                        <select name="from_mikoa_id" class="form-control" required>
                            <option value="">Chagua eneo la kuanzia</option>
                            @foreach($mikoa as $mkoa)
                                <option value="{{ $mkoa->id }}" {{ old('from_mikoa_id') == $mkoa->id ? 'selected' : '' }}>
                                    {{ $mkoa->regional_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mahali gari linapoanza safari</small>
                    </div>
                    <div class="form-group col-md-1 text-center d-none d-md-block pt-3">
                        <i class="icon-arrow-right13 text-muted" style="font-size: 1.25rem;"></i>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold text-slate-700">
                            <i class="icon-flag3 text-primary mr-1"></i> Kwenda (mwisho)
                        </label>
                        <select name="to_mikoa_id" class="form-control" required>
                            <option value="">Chagua eneo la mwisho</option>
                            @foreach($mikoa as $mkoa)
                                <option value="{{ $mkoa->id }}" {{ old('to_mikoa_id') == $mkoa->id ? 'selected' : '' }}>
                                    {{ $mkoa->regional_name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mahali pa mwisho wa safari</small>
                    </div>
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="icon-plus2 mr-1"></i> Ongeza Route
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

<h5 class="font-weight-bold text-slate-700 mb-3">
    <i class="icon-list mr-1"></i> Routes Zilizosajiliwa
    @if($routesCount > 0)
        <span class="badge badge-light border ml-1">{{ $routesCount }}</span>
    @endif
</h5>

@forelse($routes as $route)
    @php
        $sortedStops = $route->stops->sortBy('stop_order');
        $intermediateStops = $sortedStops->where('is_final', false);
        $finalStop = $sortedStops->firstWhere('is_final', true);
        $fromName = $route->fromMikoa->regional_name ?? '?';
        $toName = $route->toMikoa->regional_name ?? '?';
    @endphp
    <div class="route-card">
        <div class="route-card-header">
            <div>
                <h5 class="route-card-title">{{ $fromName }} → {{ $toName }}</h5>
                <div class="route-card-meta">
                    Kutoka {{ $fromName }} hadi {{ $toName }}
                </div>
            </div>
            <form action="{{ route('cargo_routes.destroy', $route->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Unahakika unataka kufuta route hii na vituo vyake vyote?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="icon-trash mr-1"></i> Futa Route
                </button>
            </form>
        </div>

        <div class="route-timeline">
            <span class="route-timeline-node from">
                <i class="icon-home7"></i> {{ $fromName }}
            </span>
            @if(config('app.allow_enroute_drops'))
            @foreach($intermediateStops as $stop)
                <span class="route-timeline-arrow"><i class="icon-arrow-right5"></i></span>
                <span class="route-timeline-node mid">
                    <i class="icon-location4"></i> {{ $stop->mikoa->regional_name ?? '?' }}
                </span>
            @endforeach
            @endif
            <span class="route-timeline-arrow"><i class="icon-arrow-right5"></i></span>
            <span class="route-timeline-node final">
                <i class="icon-flag3"></i> {{ $finalStop->mikoa->regional_name ?? $toName }}
            </span>
        </div>

        <div class="route-stops-section">
            <div class="route-stops-title">Maelezo ya vituo</div>

            <div class="route-stop-row">
                <div>
                    <span class="badge badge-info mr-2">Kutoka</span>
                    <span class="route-stop-name">{{ $fromName }}</span>
                </div>
                <span class="text-muted small">Mwanzo wa safari</span>
            </div>

            @if(config('app.allow_enroute_drops'))
            @foreach($intermediateStops as $stop)
                <div class="route-stop-row">
                    <div>
                        <span class="badge badge-secondary mr-2">Njiani</span>
                        <span class="route-stop-name">{{ $stop->mikoa->regional_name ?? '?' }}</span>
                        <span class="route-stop-order">#{{ $stop->stop_order }}</span>
                    </div>
                    <form action="{{ route('cargo_routes.stops.destroy', [$route->id, $stop->id]) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Futa kituo hiki cha njiani?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-outline-danger btn-sm">
                            <i class="icon-cross2"></i> Futa
                        </button>
                    </form>
                </div>
            @endforeach
            @endif

            <div class="route-stop-row" style="background: #eff6ff; border-color: #bfdbfe;">
                <div>
                    <span class="badge badge-primary mr-2">Mwisho</span>
                    <span class="route-stop-name">{{ $finalStop->mikoa->regional_name ?? $toName }}</span>
                </div>
                <span class="text-muted small">Huundwa kiotomatiki</span>
            </div>

            @if(config('app.allow_enroute_drops') && !$mikoa->isEmpty())
                <div class="route-add-stop">
                    <form action="{{ route('cargo_routes.stops.store', $route->id) }}" method="POST" class="form-row align-items-end">
                        @csrf
                        <div class="form-group col-md-5 mb-0">
                            <label>Ongeza kituo cha njiani</label>
                            <select name="mikoa_id" class="form-control form-control-sm" required>
                                <option value="">Chagua eneo la kushusha</option>
                                @foreach($mikoa as $mkoa)
                                    @php
                                        $alreadyOnRoute = $route->stops->contains('mikoa_id', $mkoa->id)
                                            || $mkoa->id == $route->from_mikoa_id
                                            || $mkoa->id == $route->to_mikoa_id;
                                    @endphp
                                    @if(!$alreadyOnRoute)
                                        <option value="{{ $mkoa->id }}">{{ $mkoa->regional_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 mb-0">
                            <label>Mpangilio <small class="text-muted">(hiari)</small></label>
                            <input type="number" name="stop_order" class="form-control form-control-sm"
                                   min="1" max="98" placeholder="Otomatiki">
                        </div>
                        <div class="form-group col-md-4 mb-0">
                            <button type="submit" class="btn btn-sm btn-success btn-block">
                                <i class="icon-plus2 mr-1"></i> Ongeza Kituo
                            </button>
                        </div>
                    </form>
                    <p class="route-flow-hint mb-0">
                        <i class="icon-info22 mr-1"></i>
                        Mizigo inashushwa kwanza kwenye vituo vya njiani, kisha mwisho. Mpangilio mdogo = kituo cha kwanza kushusha.
                    </p>
                </div>
            @endif
        </div>
    </div>
@empty
    <div class="routes-empty">
        <div><i class="icon-road d-block"></i></div>
        <h6 class="font-weight-bold text-slate-700">Hakuna routes bado</h6>
        <p class="mb-0">
            @if($mikoa->isEmpty())
                Sajili mikoa kwanza, kisha unda route yako ya kwanza hapa juu.
            @else
                Tumia fomu ya "Ongeza Route Mpya" hapa juu kuunda safari ya kwanza (mfano: Dar es Salaam → Mwanza).
            @endif
        </p>
    </div>
@endforelse
@endsection
