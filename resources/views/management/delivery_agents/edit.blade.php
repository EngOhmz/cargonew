@extends('layout.master')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Delivery Agent</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('delivery_agents.update', $agent->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $agent->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username (will be used as Email)</label>
                                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $agent->username) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Mobile Number</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $agent->phone) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="destination_mikoa_id">Destination region (optional)</label>
                                    <select name="destination_mikoa_id" id="destination_mikoa_id" class="form-control">
                                        <option value="">- None -</option>
                                        @foreach ($mikoa as $m)
                                            <option value="{{ $m->id }}" {{ (string) old('destination_mikoa_id', $agent->destination_mikoa_id) === (string) $m->id ? 'selected' : '' }}>
                                                {{ $m->regional_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Source region stays the company default unless you change it in data; new agents get the default on create.</small>
                                </div>
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="{{ route('delivery_agents.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
