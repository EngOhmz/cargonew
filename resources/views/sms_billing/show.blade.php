@extends('layout.master')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">SMS Usage - {{ $company->cname }}</h5>
            <div>
                <a href="{{ route('sms_billing.index', ['from' => $from, 'to' => $to]) }}" class="btn btn-secondary btn-sm">
                    <i class="icon-arrow-left13"></i> Rudi kwa SMS Billing
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <p class="mb-1"><strong>Email:</strong> {{ $company->email }}</p>
                <p class="mb-0"><strong>Contact:</strong> {{ $company->contactName ?? 'N/A' }}</p>
            </div>

            <form method="GET" action="{{ route('sms_billing.show', $company->id) }}" class="form-inline mb-4">
                <label class="mr-2 font-weight-semibold">Kipindi:</label>
                <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ $from }}">
                <span class="mr-2">-</span>
                <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ $to }}">
                <button type="submit" class="btn btn-sm btn-primary">Chuja</button>
            </form>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted font-size-sm">Zilizotumwa (kipindi)</div>
                        <div class="font-size-lg font-weight-bold text-success">{{ number_format($counts['sent']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted font-size-sm">Zilizoshindwa</div>
                        <div class="font-size-lg font-weight-bold text-danger">{{ number_format($counts['failed']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center">
                        <div class="text-muted font-size-sm">Zinasubiri</div>
                        <div class="font-size-lg font-weight-bold text-warning">{{ number_format($counts['pending']) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-3 text-center bg-light">
                        <div class="text-muted font-size-sm">Jumla zilizotumwa (wakati wote)</div>
                        <div class="font-size-lg font-weight-bold text-primary">{{ number_format($allTimeCounts['sent']) }}</div>
                    </div>
                </div>
            </div>

            <p class="text-muted mb-3">
                <strong>Billing:</strong> hesabu kwa SMS zenye status <code>sent</code> tu kwa kipindi kilichochaguliwa.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tarehe</th>
                            <th>Simu</th>
                            <th>Status</th>
                            <th>Ujumbe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $index => $sms)
                            <tr>
                                <td>{{ $logs->firstItem() + $index }}</td>
                                <td class="text-nowrap">{{ $sms->created_at ? $sms->created_at->format('Y-m-d H:i') : '-' }}</td>
                                <td>{{ $sms->phone_number ?? '-' }}</td>
                                <td>
                                    @if($sms->status === 'sent')
                                        <span class="badge badge-success">sent</span>
                                    @elseif($sms->status === 'failed')
                                        <span class="badge badge-danger">failed</span>
                                    @else
                                        <span class="badge badge-warning">{{ $sms->status }}</span>
                                    @endif
                                </td>
                                <td style="max-width: 420px; white-space: normal;">{{ Str::limit($sms->message, 160) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Hakuna SMS kwa kipindi hiki.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
