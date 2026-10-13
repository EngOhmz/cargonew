@extends('layout.master')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Registered Companies</h5>
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

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Contact Name</th>
                            <th>Users Count</th>
                            <th>Trial Start</th>
                            <th>Trial End</th>
                            <th>Subscription Start</th>
                            <th>Subscription End</th>
                            <th>SMS Sender</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $index => $company)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $company->cname }}</td>
                                <td>{{ $company->email }}</td>
                                <td>{{ $company->contactName }}</td>
                                <td>{{ $company->users->count() }}</td>
                                <td>{{ $company->trial_start_at ? $company->trial_start_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>
                                    {{ $company->trial_end_at ? $company->trial_end_at->format('Y-m-d H:i') : 'N/A' }}
                                    @if($company->trial_end_at && $company->trial_end_at->isPast())
                                        <span class="badge badge-danger">Expired</span>
                                    @elseif($company->trial_end_at && $company->trial_end_at->isFuture())
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                                <td>{{ $company->subscription_start_at ? $company->subscription_start_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                <td>
                                    {{ $company->subscription_end_at ? $company->subscription_end_at->format('Y-m-d H:i') : 'N/A' }}
                                    @if($company->subscription_end_at && $company->subscription_end_at->isPast())
                                        <span class="badge badge-danger">Expired</span>
                                    @elseif($company->subscription_end_at && $company->subscription_end_at->isFuture())
                                        <span class="badge badge-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if($company->sms_sender_id)
                                        <span class="badge badge-primary">{{ $company->sms_sender_id }}</span>
                                    @else
                                        <span class="text-muted">MADEKENYA</span>
                                    @endif
                                </td>
                                <td>
                                    @if($company->is_platform ?? false)
                                        <span class="badge badge-info">Platform</span>
                                    @elseif($company->is_disabled)
                                        <span class="badge badge-danger">Disabled</span>
                                    @else
                                        <span class="badge badge-success">Enabled</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!($company->is_platform ?? false))
                                    <a href="{{ route('registered_companies.edit', $company->id) }}" class="btn btn-sm btn-primary">
                                        <i class="icon-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('registered_companies.toggle_disabled', $company->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm {{ $company->is_disabled ? 'btn-success' : 'btn-warning' }}"
                                                onclick="return confirm('Are you sure you want to {{ $company->is_disabled ? 'enable' : 'disable' }} this company?')">
                                            <i class="icon-{{ $company->is_disabled ? 'check' : 'blocked' }}"></i>
                                            {{ $company->is_disabled ? 'Enable' : 'Disable' }}
                                        </button>
                                    </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No registered companies found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
