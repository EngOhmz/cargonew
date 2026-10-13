@extends('layout.master')

@section('content')
<div class="container-fluid px-4 pb-5">
    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        @if(auth()->user()->hasRole('superadmin'))
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 kpi-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-indigo-100 p-3 rounded-lg text-indigo-700">
                            <i class="icon-coin-dollar icon-2x"></i>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-flat border-indigo text-indigo px-2 py-1">MAPATO</span>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-1 text-slate-800">{{ number_format($totalRevenue) }} TZS</h3>
                    <div class="d-flex align-items-center">
                        <span class="text-success font-weight-bold mr-2">
                            <i class="icon-arrow-up8"></i> {{ number_format($todayRevenue) }}
                        </span>
                        <span class="text-muted small">Yaliyokusanywa leo</span>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                        <span class="small text-muted">Bado yanadaiwa:</span>
                        <span class="small font-weight-bold text-danger">{{ number_format($pendingRevenue) }} TZS</span>
                    </div>
                </div>
                <div class="card-progress bg-indigo-500" style="width: 70%;"></div>
            </div>
        </div>

        <!-- Shipment Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 kpi-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-blue-100 p-3 rounded-lg text-blue-700">
                            <i class="icon-package icon-2x"></i>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-flat border-blue text-blue px-2 py-1">MIZIGO</span>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-1 text-slate-800">{{ number_format($totalMizigo) }} Idadi</h3>
                    <div class="d-flex align-items-center">
                        <span class="text-blue font-weight-bold mr-2">
                            <i class="icon-truck"></i> {{ $todayMizigo }}
                        </span>
                        <span class="text-muted small">Iliyosajiliwa leo</span>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                        <span class="small text-muted">Kwa wiki:</span>
                        <span class="small font-weight-bold">{{ number_format($weekMizigo) }}</span>
                    </div>
                </div>
                <div class="card-progress bg-blue-500" style="width: 85%;"></div>
            </div>
        </div>
        @endif

        <!-- Fleet Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 kpi-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-teal-100 p-3 rounded-lg text-teal-700">
                            <i class="icon-truck icon-2x"></i>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-flat border-teal text-teal px-2 py-1">FLEET</span>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-1 text-slate-800">{{ $activeCarsCount + $inTransitCount + $pendingCarsCount }} Shughuli</h3>
                    <div class="d-flex align-items-center mb-1">
                        <span class="text-teal font-weight-bold mr-2">
                            <i class="icon-spinner11 spinner"></i> {{ $inTransitCount }}
                        </span>
                        <span class="text-muted small">Ipo njiani</span>
                    </div>
                    <div class="d-flex align-items-center mb-1">
                        <span class="text-primary font-weight-bold mr-2">
                            <i class="icon-package"></i> {{ $activeCarsCount }}
                        </span>
                        <span class="text-muted small">Inapakiwa</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-orange font-weight-bold mr-2">
                            <i class="icon-history"></i> {{ $pendingCarsCount }}
                        </span>
                        <span class="text-muted small">Inasubiri</span>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                            <span class="small text-muted">Imefika:</span>
                            <span class="small font-weight-bold text-success">{{ $arrivedCarsCount }}</span>
                        </div>
                        <div>
                            <span class="small text-muted">Imefungwa:</span>
                            <span class="small font-weight-bold text-slate-500">{{ $closedCarsCount }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-progress bg-teal-500" style="width: 60%;"></div>
            </div>
        </div>

        @if(auth()->user()->hasRole('superadmin'))
        <!-- Customer Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 kpi-card overflow-hidden">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="bg-orange-100 p-3 rounded-lg text-orange-700">
                            <i class="icon-users4 icon-2x"></i>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-flat border-orange text-orange px-2 py-1">WATEJA</span>
                        </div>
                    </div>
                    <h3 class="font-weight-bold mb-1 text-slate-800">{{ number_format($totalCustomers) }}</h3>
                    <div class="d-flex align-items-center">
                        <span class="text-orange font-weight-bold mr-2">
                            <i class="icon-user-plus"></i> {{ $todayCustomers }}
                        </span>
                        <span class="text-muted small">Wateja wapya leo</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @if(auth()->user()->hasRole('superadmin'))
    <!-- Monitoring Tables Section -->
    <div class="row mb-4">
        <!-- Live Route Monitor -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-700 mb-0">Active Route Monitor</h6>
                    <a href="{{ route('all_car') }}" class="btn btn-link btn-sm font-weight-bold text-indigo p-0">Detailed Fleet View</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle custom-table">
                        <thead class="bg-slate-50 text-slate-500 text-uppercase small font-weight-bold">
                            <tr>
                                <th class="px-4">Asset / Driver</th>
                                <th>Operational Route</th>
                                <th>Status</th>
                                <th class="text-right px-4">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($openRoutes as $route)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-indigo-100 text-indigo-700 rounded mr-3">
                                                <i class="icon-truck"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-slate-800">{{ $route->car_name }}</div>
                                                <div class="small text-muted">{{ $route->driver_name ?? 'Assignment Pending' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="font-weight-medium">{{ $route->from }}</span>
                                            <i class="icon-arrow-right13 mx-3 text-slate-300"></i>
                                            <span class="font-weight-medium">{{ $route->to }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $carStatusLabel = '';
                                            $carStatusClass = '';
                                            switch ((int)($route->car_status)) {
                                                case 0: $carStatusLabel = 'Ipo njiani'; $carStatusClass = 'status-active'; break;
                                                case 1: $carStatusLabel = 'Imefika'; $carStatusClass = 'status-ready'; break;
                                                case 2: $carStatusLabel = 'Inapakiwa'; $carStatusClass = 'status-active'; break;
                                                case 3: $carStatusLabel = 'Imefungwa'; $carStatusClass = 'status-closed'; break;
                                                case 5: $carStatusLabel = 'Inasubiri'; $carStatusClass = 'status-pending'; break;
                                                default: $carStatusLabel = $route->car_status; $carStatusClass = 'status-ready';
                                            }
                                        @endphp
                                        <span class="status-indicator {{ $carStatusClass }}">{{ $carStatusLabel }}</span>
                                    </td>
                                    <td class="text-right px-4">
                                        <a href="{{ route('car_today_routes', $route->car_id) }}" class="btn btn-icon btn-light rounded-circle">
                                            <i class="icon-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <img src="{{ asset('global_assets/images/placeholders/placeholder.jpg') }}" class="mb-3 opacity-50" width="80" alt="">
                                        <p class="text-muted">No active routes monitored at the moment.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grids -->
    <div class="row">
        <!-- Delivery Distribution -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="font-weight-bold text-slate-700 mb-0">Daily Drop-off Point Intelligence</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse ($todayDropPoints as $dp)
                            <div class="list-group-item d-flex align-items-center border-0 px-4 py-3 hover-bg-slate-50 transition-all">
                                <div class="bg-amber-100 text-amber-700 p-2 rounded mr-3">
                                    <i class="icon-location4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="font-weight-bold text-slate-800">{{ $dp->drop_off_point }}</div>
                                    <div class="small text-muted">{{ $dp->car_name }} &bull; {{ $dp->driver_name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-weight-bold text-slate-800">{{ $dp->total_kushusha }} items</div>
                                    <div class="small text-success">Unloaded Today</div>
                                </div>
                            </div>
                        @empty
                            <div class="py-5 text-center text-muted">No drop-off activity recorded today.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Logistics Log -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="font-weight-bold text-slate-700 mb-0">Recent Logistics Log</h6>
                    <a href="{{ route('list_ya_wateja.index') }}" class="btn btn-light btn-xs">View Log</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-table table-sm">
                            <tbody>
                                @forelse ($latestToday as $customer)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs bg-slate-100 text-slate-600 rounded-circle mr-2 d-flex align-items-center justify-content-center font-weight-bold small">
                                                    {{ strtoupper(substr($customer->mpokeaji ?: $customer->mteja, 0, 1)) }}
                                                </div>
                                                <div class="font-weight-bold text-slate-700 text-truncate" style="max-width: 120px;">{{ $customer->mpokeaji ?: $customer->mteja }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted">Mteja</div>
                                            <div class="small font-weight-medium text-slate-700">{{ $customer->mteja }}</div>
                                        </td>
                                        <td class="text-right px-4">
                                            <span class="badge badge-light text-slate-500 font-weight-normal">{{ $customer->created_at->format('H:i') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted small">No new shipment activity logged today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<style>
    /* Logistics Intelligence Theme */
    :root {
        --indigo-500: #6366f1;
        --blue-500: #3b82f6;
        --teal-500: #14b8a6;
        --green-500: #22c55e;
        --orange-500: #f59e0b;
        --amber-500: #f59e0b;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-800: #1e293b;
    }

    body { background-color: #f1f4f9; }
    
    .kpi-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 12px; }
    .kpi-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
    
    .main-card { border-radius: 12px; }
    
    .card-progress { height: 4px; position: absolute; bottom: 0; left: 0; transition: width 1s ease-in-out; }
    
    .bg-indigo-100 { background-color: #e0e7ff; }
    .bg-blue-100 { background-color: #dbeafe; }
    .bg-teal-100 { background-color: #ccfbf1; }
    .bg-orange-100 { background-color: #ffedd5; }
    .bg-amber-100 { background-color: #fef3c7; }
    .bg-success-100 { background-color: #dcfce7; }
    
    .text-indigo-700 { color: #4338ca; }
    .text-blue-700 { color: #1d4ed8; }
    .text-teal-700 { color: #0f766e; }
    .text-orange-700 { color: #c2410c; }
    .text-amber-700 { color: #b45309; }
    
    .bg-indigo-500 { background-color: var(--indigo-500); }
    .bg-blue-500 { background-color: var(--blue-500); }
    .bg-green-500 { background-color: var(--green-500); }
    
    .avatar-sm { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    .avatar-xs { width: 30px; height: 30px; }
    
    .status-indicator { 
        padding: 4px 12px; 
        border-radius: 50px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        display: inline-flex; 
        align-items: center; 
    }
    .status-indicator::before { 
        content: ''; 
        width: 6px; 
        height: 6px; 
        border-radius: 50%; 
        margin-right: 8px; 
    }
    .status-active { background-color: #e0e7ff; color: #4338ca; }
    .status-active::before { background-color: #4338ca; animation: pulse 2s infinite; }
    .status-ready { background-color: #dcfce7; color: #15803d; }
    .status-ready::before { background-color: #15803d; }
    .status-closed { background-color: #f1f5f9; color: #64748b; }
    .status-closed::before { background-color: #64748b; }
    .status-pending { background-color: #fff7ed; color: #c2410c; }
    .status-pending::before { background-color: #c2410c; animation: pulse 2s infinite; }
    
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(67, 56, 202, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(67, 56, 202, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(67, 56, 202, 0); }
    }
    
    .custom-table th { border-bottom: none; }
    .custom-table td { border-top: 1px solid #f1f5f9; vertical-align: middle; }
    
    .hover-bg-slate-50:hover { background-color: var(--slate-50); }
    .transition-all { transition: all 0.2s ease; }
    
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
</style>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // No charts needed
    });
</script>
@endsection



