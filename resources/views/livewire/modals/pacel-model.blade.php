<div>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="float: right;color:white;margin:40px">

        <i class="icon-add"></i>    Ongeza Mzigo

    </button>
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true"> 

        <div class="modal-dialog modal-lg" role="document">
            
            <div class="modal-content">
            
             
                <form wire:submit.prevent="submit">
                    <div class="modal-body">
                         <div style="color:black" >
                                <h4 >Ongeza Mzigo</h4>
                        </div>
                        <div class="form-group form-group-float">
                            <label class="form-group-float-label">jina la mzigo</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="Jina la mzigo"
                                required>
                        </div>

                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" wire:model="showDiv" value="yes"
                                name="gharama" id="cr_l_s_s">
                            <label class="custom-control-label" style="color:grey" for="cr_l_s_s">Gharama za kusafirisha kwa kila
                                moja</label>
                        </div>

                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" class="custom-control-input" wire:model="showDiv" value="no"
                                name="gharama" id="cr_l_s_u">
                            <label class="custom-control-label" style="color:grey" for="cr_l_s_u">Gharama ya kusafirisha zote</label>
                        </div>
                        @if ($showDiv == 'yes')
                            <div class="form-group form-group-float">
                                <label class="form-group-float-label">idadi ya mizigo</label>
                                <input type="numeric" class="form-control" wire:model="idadi"
                                    @if ($showDiv == 'no') wire:change="get_idadi()" readonly value="{{ $idadi }}" @endif
                                    placeholder="Idadi ya mizigo">

                                @error('idadi')
                                    <span class="error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group form-group-float">
                                <label class="form-group-float-label">bei ya kila mzigo</label>
                                <input type="numeric" class="form-control" wire:model="bei" wire:change="getTotal()"
                                    placeholder="Bei ya kila mzigo">


                            </div>
                        @endif


                        <div class="form-group form-group-float">
                            <label class="form-group-float-label">jumla kuu</label>
                            <input type="numeric" class="form-control" wire:model="jumla"
                                @if ($showDiv == 'yes') readonly  value="{{ $jumla }}" @endif
                                placeholder="Jumla kuu">
                        </div>
                        <div class="form-group form-group-float">
                            <label class="form-group-float-label">Kiasi kilichopelekwa</label>
                            <input type="numeric" class="form-control" wire:model="ela_iliyopokelewa"
                                placeholder="Kiasi kilichopokelewa">
                        </div>
                        <div class="form-group form-group-float">
                            <label class="form-group-float-label">Mzigo ulipotoka</label>
                            <input type="text" class="form-control" wire:model="mzigo_unapotoka"
                                placeholder="Mzigo ulipotoka">
                        </div>
                        <div class="form-group form-group-float">
                            <label class="form-group-float-label">Mzigo unapokwenda</label>
                            <input type="text" class="form-control" wire:model="mzigo_unapokwenda"
                                placeholder="Mzigo unapokwenda">
                        </div>

                        <div class="border p-3 rounded">
                            <label class="">Je ?</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" wire:model="receipt" value="R"
                                    name="receipt" id="cr_l_i_s" checked>
                                <label class="custom-control-label" style="color:grey" for="cr_l_i_s">Una Risiti</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" wire:model="receipt" value="HR"
                                    name="receipt" id="cr_l_i_u">
                                <label class="custom-control-label" style="color:grey" for="cr_l_i_u">Hauna Risiti</label>
                            </div>
                            
                        </div>
                         <div class="modal-footer">

                        <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">Close</button>

                        <button type="submit" class="btn btn-primary close-modal">Save</button>

                    </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<style>
    /*When the modal fills the screen it has an even 2.5% on top and bottom*/
    /*Centers the modal*/
    .modal-dialog {
        margin: 2.5vh auto;
    }

    /*Sets the maximum height of the entire modal to 95% of the screen height*/
    .modal-content {
        max-height: 95vh;
        overflow: scroll;
    }

    /*Sets the maximum height of the modal body to 90% of the screen height*/
    .modal-body {
        max-height: 90vh;
    }

    /*Sets the maximum height of the modal image to 69% of the screen height*/
    .modal-body img {
        max-height: 69vh;
    }
</style>
