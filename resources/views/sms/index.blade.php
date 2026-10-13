@extends('layout.master')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Usimamizi wa SMS</h4>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="alert alert-info">
                                SMS zote nne zimehifadhiwa lakini <strong>zimezimwa</strong> - hazitumwi hadi ziwashwe.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Aina ya SMS</th>
                                            <th>Hali</th>
                                            <th>Ujumbe (Message)</th>
                                            <th>Kitendo (Action)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sms_list as $sms)
                                            @php
                                                $enabled = $sms->isSendingEnabled();
                                            @endphp
                                            <tr class="{{ $enabled ? '' : 'table-secondary' }}">
                                                <td>{{ $sms->template_key }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $sms->template_label }}</span>
                                                </td>
                                                <td>
                                                    @if($enabled)
                                                        <span class="badge badge-success">Inatumika</span>
                                                    @else
                                                        <span class="badge badge-secondary">Imezimwa</span>
                                                    @endif
                                                </td>
                                                <td>{{ $sms->message }}</td>
                                                <td>
                                                    <a href="{{ route('sms.edit', $sms->id) }}" class="btn btn-primary btn-sm">
                                                        Badilisha
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
