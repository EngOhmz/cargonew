@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Upload Company Logo</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('company-logo.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="company_logo">Choose Logo</label>
                        <input type="file" name="company_logo" id="company_logo" class="form-control-file" onchange="previewImage(this)">
                        <small class="form-text text-muted">Supported formats: jpeg, jpg, png, gif, svg. Max size 4MB.</small>
                        @error('company_logo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- New Preview Section -->
                    <div id="preview-container" class="mt-3" style="display: none;">
                        <p class="font-weight-bold mb-1">Preview ya Mzigo utakaopandishwa:</p>
                        <img id="image-preview" src="#" alt="Logo Preview" class="img-fluid rounded border" style="max-height: 120px;">
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Upload</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Current Logo</h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ $currentLogoUrl }}" alt="Company Logo" class="img-fluid rounded shadow-sm mb-3" style="max-height: 160px;">
                <p class="text-muted mb-3">Hii ndio picha inayoonekana sasa.</p>
                @if($hasLogo)
                    <form action="{{ route('company-logo.destroy') }}" method="POST" onsubmit="return confirm('Remove current company logo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Remove Logo</button>
                    </form>
                @else
                    <p class="text-muted small mb-0">No company logo uploaded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('image-preview');
    var container = document.getElementById('preview-container');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        container.style.display = 'none';
    }
}
</script>
@endsection

