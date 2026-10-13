@extends('layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Document</h5>
                <span class="text-muted">Branch, contact and tunasafirisha text used on delivery/print documents</span>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('document_settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Logo settings --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Select Logo to use on Documents</label>
                        <div class="d-flex align-items-center">
                            <div class="form-check mr-4">
                                <input class="form-check-input" type="radio" name="logo_type" id="logo_emasuite" value="emasuite" {{ old('logo_type', optional($doc)->logo_type ?? 'emasuite') == 'emasuite' ? 'checked' : '' }}>
                                <label class="form-check-label" for="logo_emasuite">
                                    Madekenya Logo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="logo_type" id="logo_system" value="system" {{ old('logo_type', optional($doc)->logo_type) == 'system' ? 'checked' : '' }}>
                                <label class="form-check-label" for="logo_system">
                                    System Logo (Company Logo)
                                </label>
                            </div>
                        </div>
                        <div id="system_logo_title_container" class="form-group mt-2" style="display: none;">
                            <label for="system_logo_title">Company Title</label>
                            <input type="text" name="system_logo_title" id="system_logo_title" class="form-control" value="{{ old('system_logo_title', optional($doc)->system_logo_title) }}" placeholder="Enter company title to display below logo">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">TIN Number</label>
                            <input type="text" name="tin_number" class="form-control" value="{{ old('tin_number', optional($doc)->tin_number) }}" placeholder="Enter TIN number">
                        </div>
                    </div>

                    <hr>

                    {{-- Document header (same layout as PDF) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Tunasafirisha parcel kutoka (Line 1)</label>
                        <input type="text" name="header_line1" class="form-control" value="{{ old('header_line1', optional($doc)->header_line1 ?? 'TUNASAFRISHA MIZIGO KUTOKA DAR ES SALAAM') }}" placeholder="e.g. TUNASAFRISHA MIZIGO KUTOKA DAR ES SALAAM">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Kwenda (Line 2)</label>
                        <input type="text" name="header_line2" class="form-control" value="{{ old('header_line2', optional($doc)->header_line2 ?? 'KWENDA NZEGA,KAHAMA,ISAKA,MASUMBWE, NA LUGUNGA') }}" placeholder="e.g. KWENDA NZEGA,KAHAMA,ISAKA,MASUMBWE, NA LUGUNGA">
                    </div>

                    <hr>
                    <h6 class="font-weight-bold">Branch & Contact (as on document)</h6>
                    <p class="text-muted small">Row 1: labels (e.g. HEAD OFFICE, BRANCH). Row 2: places. Row 3: contact/phone.</p>

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary mb-2">Head Office</h6>
                            <div class="form-group">
                                <label>Office 1 - Label</label>
                                <input type="text" name="office_1_label" class="form-control" value="{{ old('office_1_label', optional($doc)->office_1_label ?? 'HEAD OFFICE') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 1 - Branch / Place</label>
                                <input type="text" name="office_1_place" class="form-control" value="{{ old('office_1_place', optional($doc)->office_1_place ?? 'DAR ES SALAAM') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 1 - Contact 1</label>
                                <input type="text" name="office_1_contact" class="form-control" value="{{ old('office_1_contact', optional($doc)->office_1_contact ?? '07xx xxx xxx') }}" placeholder="e.g. 0753-326 537">
                            </div>
                            <div class="form-group">
                                <label>Office 1 - Contact 2 <span class="text-muted small">(add contact)</span></label>
                                <input type="text" name="office_1_contact_2" class="form-control" value="{{ old('office_1_contact_2', optional($doc)->office_1_contact_2) }}" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label>Office 1 - Contact 3 <span class="text-muted small">(add contact)</span></label>
                                <input type="text" name="office_1_contact_3" class="form-control" value="{{ old('office_1_contact_3', optional($doc)->office_1_contact_3) }}" placeholder="Optional">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Office 2 - Label</label>
                                <input type="text" name="office_2_label" class="form-control" value="{{ old('office_2_label', optional($doc)->office_2_label ?? 'BRANCH') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 2 - Branch / Place</label>
                                <input type="text" name="office_2_place" class="form-control" value="{{ old('office_2_place', optional($doc)->office_2_place ?? 'KAHAMA') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 2 - Contact</label>
                                <input type="text" name="office_2_contact" class="form-control" value="{{ old('office_2_contact', optional($doc)->office_2_contact ?? '07xx xxx xxx') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Office 3 - Label</label>
                                <input type="text" name="office_3_label" class="form-control" value="{{ old('office_3_label', optional($doc)->office_3_label ?? 'BRANCH') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 3 - Branch / Place</label>
                                <input type="text" name="office_3_place" class="form-control" value="{{ old('office_3_place', optional($doc)->office_3_place ?? 'NZEGA') }}">
                            </div>
                            <div class="form-group">
                                <label>Office 3 - Contact</label>
                                <input type="text" name="office_3_contact" class="form-control" value="{{ old('office_3_contact', optional($doc)->office_3_contact ?? '07xx xxx xxx') }}">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold">Kanuni (Rules)</h6>
                    <p class="text-muted small">Edit the rules displayed at the bottom of PDF documents.</p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kanuni 1</label>
                                <textarea name="kanuni_1" class="form-control" rows="2">{{ old('kanuni_1', optional($doc)->kanuni_1 ?? 'MTEJA HAKIKISHA UMEFUNGA MZIGO WAKO IMARA') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Kanuni 2</label>
                                <textarea name="kanuni_2" class="form-control" rows="2">{{ old('kanuni_2', optional($doc)->kanuni_2 ?? 'MTEJA MALI YOYOTE ITAKAYO HARIBIKA NJIANI HATUTOKUWA NA DHAMANA NAYO KUTOKANA NA HALI YA BARABARA NI MUHIMU KUKATIA MZIGO WAKO BIMA KWA KUWA MSAFIRISHAJI HATOHUSIKA NA BIMA YA MZIGO WAKO') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kanuni 3</label>
                                <textarea name="kanuni_3" class="form-control" rows="2">{{ old('kanuni_3', optional($doc)->kanuni_3 ?? 'MZIGO WA KUVUNJIKA KAMA VILE VIGAE,VIOO,TV N.K HATUTAHUSIKA NA UHARIBIFU WOWOTE UTAKAO JITOKEZA') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Kanuni 4</label>
                                <textarea name="kanuni_4" class="form-control" rows="2">{{ old('kanuni_4', optional($doc)->kanuni_4 ?? 'HATUPOKEI MALALAMIKO YOYOTE KUHUSU MZIGO BAADA YA SIKU 20 TANGU KUPOKEA') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save document settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoTypeRadios = document.querySelectorAll('input[name="logo_type"]');
        const titleContainer = document.getElementById('system_logo_title_container');

        function toggleTitleField() {
            if (document.querySelector('input[name="logo_type"]:checked').value === 'system') {
                titleContainer.style.display = 'block';
            } else {
                titleContainer.style.display = 'none';
            }
        }

        logoTypeRadios.forEach(radio => {
            radio.addEventListener('change', toggleTitleField);
        });

        // Initial check on page load
        toggleTitleField();
    });
</script>
@endpush
