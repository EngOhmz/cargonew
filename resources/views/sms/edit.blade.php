@extends('layout.master')

@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-sm-12 col-lg-8 offset-lg-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Hariri Kiolezo cha SMS - {{ $sms->template_label }}</h4>
                        </div>
                        <div class="card-body">
                            @if(!$sms->isSendingEnabled())
                                <div class="alert alert-warning">
                                    Kiolezo hiki <strong>kimezimwa</strong> - hakitumwa kwa wateja.
                                    Unaweza kuhariri ujumbe, lakini SMS haitatumwa hadi kiwashwe na msimamizi wa mfumo.
                                </div>
                            @else
                                <div class="alert alert-success">
                                    Kiolezo hiki <strong>kinatumika</strong> - SMS hutumwa kiotomatiki wakati husika.
                                </div>
                            @endif

                            <form action="{{ route('sms.update', $sms->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="message">Maudhui ya Ujumbe (Message Content)</label>
                                    <textarea name="message" id="message" class="form-control" rows="6" required>{{ old('message', $sms->message) }}</textarea>
                                    <small class="form-text text-muted">
                                        Vitumika (Placeholders):<br>
                                        <code>[Company]</code> - Jina la kampuni<br>
                                        <code>[Supplier]</code> - Jina la mtumaji<br>
                                        <code>[Packages]</code> - Idadi / maelezo ya mizigo<br>
                                        <code>[Ref]</code> - Namba ya REF ya mzigo<br>
                                        <code>[Vehicle]</code> - Namba ya gari<br>
                                        <code>[Destination]</code> - Sehemu mzigo umefikia<br>
                                        <code>[Amount]</code> - Kiasi cha fedha (TZS)
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Hali ya kutuma</label>
                                    <input type="text" class="form-control"
                                           value="{{ $sms->isSendingEnabled() ? 'Inatumika' : 'Imezimwa' }}"
                                           readonly>
                                </div>

                                <div class="text-right">
                                    <a href="{{ route('sms.index') }}" class="btn btn-secondary">Ghairi</a>
                                    <button type="submit" class="btn btn-primary">Hifadhi Mabadiliko</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
