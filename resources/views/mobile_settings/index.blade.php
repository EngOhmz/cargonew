@extends('layout.master')

@push('plugin-styles')
{!! Html::style('assets/css/loader.css') !!}
{!! Html::style('plugins/fileupload/fileupload.css') !!}
@endpush

@section('content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Mobile App Settings - App Details</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
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

                        <form action="{{ route('mobile_settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="app_name">App Company Name</label>
                                <input type="text" 
                                       class="form-control @error('app_name') is-invalid @enderror" 
                                       id="app_name" 
                                       name="app_name" 
                                       value="{{ old('app_name', $company->app_name ?? '') }}" 
                                       placeholder="Enter app company name">
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">This name will be displayed in the mobile app</small>
                            </div>

                            <div class="form-group">
                                <label for="app_logo">App Logo</label>
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input @error('app_logo') is-invalid @enderror" 
                                           id="app_logo" 
                                           name="app_logo" 
                                           accept="image/*">
                                    <label class="custom-file-label" for="app_logo">Choose file</label>
                                    @error('app_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Upload logo for mobile app (Max: 2MB, Formats: JPEG, PNG, JPG, GIF)</small>
                                
                                @if($company->app_logo)
                                    <div class="mt-3">
                                        <p>Current Logo:</p>
                                        <img src="{{ asset('storage/' . $company->app_logo) }}" 
                                             alt="App Logo" 
                                             style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-checkmark2"></i> Save Settings
                                </button>
                                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                    <i class="icon-cross2"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('plugin-scripts')
{!! Html::script('plugins/fileupload/fileupload.min.js') !!}
<script>
    // Update custom file input label
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endpush

