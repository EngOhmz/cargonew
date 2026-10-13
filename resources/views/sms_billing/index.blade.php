@extends('layout.master')

@section('content')
<div class="content">
    <div class="card mb-3">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">SMS Billing</h5>
            <span class="text-muted">Matumizi ya SMS kwa kampuni zilizosajiliwa</span>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form method="GET" action="{{ route('sms_billing.index') }}" class="form-inline mb-4">
                <label class="mr-2 font-weight-semibold">Kipindi:</label>
                <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ $from }}">
                <span class="mr-2">-</span>
                <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ $to }}">
                <button type="submit" class="btn btn-sm btn-primary">Chuja</button>
            </form>

            <div class="row mb-2">
                <div class="col-md-3">
                    <div class="bg-success text-white p-3 rounded text-center">
                        <div class="font-size-sm opacity-75">Zilizotumwa</div>
                        <div class="font-size-lg font-weight-bold">{{ number_format($platformTotals['sent']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-danger text-white p-3 rounded text-center">
                        <div class="font-size-sm opacity-75">Zilizoshindwa</div>
                        <div class="font-size-lg font-weight-bold">{{ number_format($platformTotals['failed']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-warning text-white p-3 rounded text-center">
                        <div class="font-size-sm opacity-75">Zinasubiri</div>
                        <div class="font-size-lg font-weight-bold">{{ number_format($platformTotals['pending']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="bg-primary text-white p-3 rounded text-center">
                        <div class="font-size-sm opacity-75">Billing (sent)</div>
                        <div class="font-size-lg font-weight-bold">{{ number_format($platformTotals['sent']) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Matumizi kwa Kampuni</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kampuni</th>
                            <th>Email</th>
                            <th class="text-center">SMS Sent</th>
                            <th class="text-center">SMS Failed</th>
                            <th class="text-center">Jumla</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $index => $company)
                            @php
                                $sms = $smsByCompany->get($company->id);
                                $sent = (int) ($sms->sent ?? 0);
                                $failed = (int) ($sms->failed ?? 0);
                                $total = (int) ($sms->total ?? 0);
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $company->cname }}</td>
                                <td>{{ $company->email }}</td>
                                <td class="text-center">
                                    <span class="badge badge-success">{{ number_format($sent) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger">{{ number_format($failed) }}</span>
                                </td>
                                <td class="text-center"><strong>{{ number_format($total) }}</strong></td>
                                <td>
                                    <a href="{{ route('sms_billing.show', $company->id) }}?from={{ $from }}&to={{ $to }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="icon-eye"></i> Angalia
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Hakuna kampuni zilizosajiliwa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
