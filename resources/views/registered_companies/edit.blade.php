@extends('layout.master')

@section('content')
<div class="content">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Edit Company: {{ $company->cname }}</h5>
            <div class="header-elements">
                <a href="{{ route('registered_companies.index') }}" class="btn btn-secondary">
                    <i class="icon-arrow-left7"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('registered_companies.update', $company->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" value="{{ $company->cname }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="{{ $company->email }}" readonly>
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-semibold">Trial Period</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Trial Start Date</label>
                            <input type="datetime-local" name="trial_start_at" class="form-control" 
                                   value="{{ $company->trial_start_at ? $company->trial_start_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Trial End Date</label>
                            <input type="datetime-local" name="trial_end_at" class="form-control" 
                                   value="{{ $company->trial_end_at ? $company->trial_end_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Extend Trial Period (Days)</label>
                    <input type="number" name="extend_trial_days" class="form-control" min="1" 
                           placeholder="Enter number of days to extend trial">
                    <small class="form-text text-muted">This will extend the trial end date by the specified number of days from today.</small>
                </div>

                <hr>
                <h6 class="font-weight-semibold">Subscription Period (After Trial)</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Subscription Start Date</label>
                            <input type="datetime-local" name="subscription_start_at" class="form-control" 
                                   value="{{ $company->subscription_start_at ? $company->subscription_start_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Subscription End Date</label>
                            <input type="datetime-local" name="subscription_end_at" class="form-control" 
                                   value="{{ $company->subscription_end_at ? $company->subscription_end_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>
                </div>

                <hr>
                <h6 class="font-weight-semibold">SMS Sender ID</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sms_sender_id">Sender ID (Imart)</label>
                            <input type="text"
                                   name="sms_sender_id"
                                   id="sms_sender_id"
                                   class="form-control @error('sms_sender_id') is-invalid @enderror"
                                   value="{{ old('sms_sender_id', $company->sms_sender_id) }}"
                                   maxlength="11"
                                   placeholder="e.g. Sanga Cargo">
                            @error('sms_sender_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Exact Sender ID from Imart portal (max 11 characters). Leave empty to use default MADEKENYA.
                            </small>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_disabled" value="1" class="form-check-input" 
                               id="is_disabled" {{ $company->is_disabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_disabled">
                            Disable Company
                        </label>
                        <small class="form-text text-muted">Disabled companies cannot access the system.</small>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-check"></i> Update Company
                    </button>
                    <a href="{{ route('registered_companies.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>

            <hr>
            <h6 class="font-weight-semibold">Company Users</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($company->users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No users found for this company.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

