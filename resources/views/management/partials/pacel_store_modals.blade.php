<div class="modal fade" id="pacelEditModal" tabindex="-1" role="dialog" aria-labelledby="pacelEditModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pacelEditModalTitle">Edit Mzigo</h5>
                <button type="button" class="close js-close-store-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pacel.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="pacel_id" id="pacel_id" value="">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Jina la Mpokeaji (Ofisi)</label>
                            <input type="text" class="form-control" name="mpokeaji" id="mpokeaji">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jina la Mteja</label>
                            <input type="text" class="form-control" name="mteja" id="mteja">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jina la mzigo</label>
                            <input type="text" name="name" class="form-control" id="pacelName" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Idadi ya mizigo</label>
                            <input type="number" class="form-control" name="idadi" id="pacelIdadi">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Bei ya kila mzigo</label>
                            <input type="number" class="form-control" name="bei" id="pacelBei">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jumla kuu</label>
                            <input type="number" class="form-control bg-light" name="jumla" id="pacelJumla" readonly>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Ela iliyopokelewa (jumla)</label>
                            <input type="number" class="form-control" name="ela_iliyopokelewa" id="pacelEla_iliyopokelewa">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Mzigo ulipotoka</label>
                            <input type="text" class="form-control" name="mzigo_unapotoka" id="pacelMzigo_unapotoka">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Mzigo unapokwenda</label>
                            <input type="text" class="form-control" name="mzigo_unapokwenda" id="pacelMzigo_unapokwenda">
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="d-block">Risiti</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="receipt" id="cr_l_i_s" value="R" checked>
                                <label class="custom-control-label" for="cr_l_i_s">Una Risiti</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" value="HR" name="receipt" id="cr_l_i_u">
                                <label class="custom-control-label" for="cr_l_i_u">Hauna Risiti</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light js-close-store-modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Mzigo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="pacelMoneyModal" tabindex="-1" role="dialog" aria-labelledby="pacelMoneyModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pacelMoneyModalTitle">Pokea Malipo</h5>
                <button type="button" class="close js-close-store-modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('money.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="pacel_id" id="pacel_id2" value="">
                    <div class="form-group mb-0">
                        <label>Kiasi cha kupokea (leo)</label>
                        <input type="number" class="form-control form-control-lg" name="amount_received" id="pacelAmount_received2" min="0" step="any" required>
                        <small class="text-muted d-block mt-2">
                            Baki ya deni: <span id="pacelRemainingDue2">0</span> TZS
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light js-close-store-modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Pokea Malipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
