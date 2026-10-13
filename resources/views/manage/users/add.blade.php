@extends('layout.master')

@push('plugin-styles')
<style>
.user-form-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.user-form-page .page-subtitle {
    color: #64748b;
    font-size: 0.875rem;
    margin: 0.25rem 0 0;
}

.user-form-page .form-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.25rem;
}

.user-form-page .form-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #6366f1;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.user-form-page .form-section-title i {
    color: #6366f1;
    font-size: 1rem;
}

.user-form-page .form-group label {
    font-weight: 600;
    color: #475569;
    margin-bottom: 0.45rem;
    font-size: 0.875rem;
}

.user-form-page .form-control {
    min-height: 46px;
    border-radius: 8px;
    border-color: #cbd5e1;
    font-size: 0.95rem;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.user-form-page .form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}

.user-form-page .form-control.is-invalid,
.user-form-page .form-control:invalid:not(:placeholder-shown) {
    border-color: #ef4444;
}

.user-form-page .invalid-feedback,
.user-form-page .field-error {
    color: #dc2626;
    font-size: 0.8rem;
    margin-top: 0.35rem;
}

.user-form-page .role-hint {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    border-radius: 10px;
    padding: 0.85rem 1rem;
    margin-top: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
}

.user-form-page .role-hint i {
    color: #4f46e5;
    margin-top: 0.1rem;
    flex-shrink: 0;
}

.user-form-page .role-hint p {
    margin: 0;
    color: #4338ca;
    font-size: 0.85rem;
    line-height: 1.5;
}

.user-form-page .form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding-top: 0.5rem;
    margin-top: 0.25rem;
}

.user-form-page .btn-save {
    min-width: 140px;
    min-height: 44px;
    border-radius: 8px;
    font-weight: 600;
    background: #4f46e5;
    border-color: #4f46e5;
}

.user-form-page .btn-save:hover {
    background: #4338ca;
    border-color: #4338ca;
}

.user-form-page .input-icon-wrap {
    position: relative;
}

.user-form-page .input-icon-wrap .form-control {
    padding-left: 2.5rem;
}

.user-form-page .input-icon-wrap .field-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
    font-size: 0.95rem;
}

.user-form-page select.form-control {
    appearance: auto;
    cursor: pointer;
}

@media (max-width: 575.98px) {
    .user-form-page .form-actions {
        flex-direction: column-reverse;
    }

    .user-form-page .form-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@section('content')
<section class="section user-form-page">
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-lg-10 col-xl-9 mx-auto">
                <div class="card shadow-sm border-0">
                    <div class="card-header page-header bg-white">
                        <div>
                            <h4 class="mb-0">Add New User</h4>
                            <p class="page-subtitle">Create a new account and assign a role with permissions</p>
                        </div>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                            <i class="icon-arrow-left13 mr-1"></i> Back
                        </a>
                    </div>

                    <div class="card-body pt-4">
                        {{ Form::open(['route' => 'users.store', 'class' => 'user-create-form']) }}
                        @method('POST')

                        {{-- Personal details --}}
                        <div class="form-section">
                            <h6 class="form-section-title">
                                <i class="icon-user"></i> Basic Information
                            </h6>
                            <div class="row">
                                <div class="col-12 form-group">
                                    <label for="name">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="icon-user field-icon"></i>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" id="name" value="{{ old('name') }}"
                                            placeholder="e.g. John Doe" required>
                                    </div>
                                    @error('name')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="email">Email / Username <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="icon-mail5 field-icon"></i>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" id="email" value="{{ old('email') }}"
                                            placeholder="user@company.com" required>
                                    </div>
                                    @error('email')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="icon-phone field-icon"></i>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                            name="phone" id="phone" value="{{ old('phone') }}"
                                            placeholder="07XXXXXXXX" required>
                                    </div>
                                    @error('phone')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Role assignment --}}
                        <div class="form-section">
                            <h6 class="form-section-title">
                                <i class="icon-cog6"></i> Assign Role
                            </h6>
                            <div class="row">
                                <div class="col-12 form-group mb-0">
                                    <label for="role_id">Select Role <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role_id') is-invalid @enderror"
                                        name="role_id" id="role_id" required>
                                        <option value="" disabled {{ old('role_id') ? '' : 'selected' }}>
                                            Choose a role for this user
                                        </option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ (string) old('role_id') === (string) $role->id ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('-', ' ', $role->slug)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="role-hint">
                                <i class="icon-info3"></i>
                                <p>
                                    The user will see menus and access features based on the permissions assigned to this role.
                                    Make sure the role has the correct permissions before creating the account.
                                </p>
                            </div>
                        </div>

                        {{-- Security --}}
                        <div class="form-section">
                            <h6 class="form-section-title">
                                <i class="icon-lock"></i> Account Security
                            </h6>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="icon-lock field-icon"></i>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" id="password" placeholder="At least 6 characters" required>
                                    </div>
                                    @error('password')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="icon-lock field-icon"></i>
                                        <input type="password" class="form-control"
                                            name="password_confirmation" id="password_confirmation"
                                            placeholder="Re-enter password" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-save">
                                <i class="icon-checkmark3 mr-1"></i> Save User
                            </button>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
