@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sajili mikoa</h5>
                <span class="text-muted">Orodha ya mikoa na maeneo kwa kampuni yako</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('mikoa.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="form-inline">
                        <label class="mr-2 font-weight-bold">Jina la mkoa/kituo:</label>
                        <input type="text" name="regional_name" class="form-control mr-2" value="{{ old('regional_name') }}" placeholder="mfano: Dar es Salaam, Chalinze, Morogoro" required maxlength="255">
                        <button type="submit" class="btn btn-primary">Ongeza</button>
                    </div>
                </form>

                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jina la mkoa</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mikoa as $mkoa)
                        <tr>
                            <td>{{ $mkoa->id }}</td>
                            <td>{{ $mkoa->regional_name }}</td>
                            <td>
                                <form action="{{ route('mikoa.destroy', $mkoa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Unahakika unataka kufuta?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Futa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-muted">Hakuna mikoa bado. Ongeza hapa juu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
