<div>
    <div wire:ignore.self class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="shop-info-modal" aria-hidden="true">
        <div class="modal-dialog model-lg">
            <div class="modal-content custom-border">
                @if(!blank($menuItem))

                <div class="modal-header custom-padding-design">
                    {{-- <img src="{{ $menuItem->image }}"> --}}
                    <button type="button" class="close custom-close-style" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <form wire:submit.prevent="submit({{$menuItem->id}})" >
                            <div id="variation-and-option" class="customize-sign-in zoom-anim-dialog custom-box-size">
                                <h3 class="product-view-header-style">{{$menuItem->name}}</h3>
                                <hr>

                                @if(!blank($menuItem->variations))
                                    <h5 class="margin-top-30 margin-bottom-10">{{ __('frontend.select_variations') }}</h5>
                                    @foreach($menuItem->variations as $variation)
                                        <div class="payment-tab-trigger">
                                            <span class="variation-price"> {{setting('currency_name')}} {{$variation->price - $variation->discount_price}}</span>
                                            <input wire:model="variationID" @if($loop->first) checked="checked" @endif id="{{$variation->name}}" name="variationID" type="radio" value="{{$variation->id}}">
                                            <label class="variation-label-name" for="{{$variation->name}}">{{$variation->name}}</label>
                                        </div>
                                    @endforeach
                                    <hr>
                                @endif

                                @if(!blank($menuItem->options))
                                    <h5 class="margin-top-30 margin-bottom-10">{{ __('frontend.select_options') }} <span>({{ __('frontend.optional') }})</span></h5>
                                    <div class="checkboxes in-row margin-bottom-20">
                                        @foreach($menuItem->options as $option)
                                            <span class="optional-price">+ {{setting('currency_name')}} {{$option->price}}</span>
                                            <input  wire:model="options" id="check-option-{{ __('other') }}-{{ $menuItem->id }}-{{ $option->id }}" type="checkbox" value="{{ $option->id }}" name="options[]">
                                            <label class="optional-checkbox" for="check-option-{{ __('other') }}-{{ $menuItem->id }}-{{ $option->id }}">{{$option->name}}</label> <br>
                                        @endforeach
                                    </div>
                                    <hr>
                                @endif
                                <h4 class="margin-bottom-10"><b>{{__('frontend.special_instructions')}}</b></h4>
                                <h5 class="margin-bottom-10">{{__('frontend.menu_subtitle')}}</h5>
                                <div class="form">
                                    <textarea class="WYSIWYG" name="instructions" style="width:100%" cols="40"  rows="3" id="instructions" wire:model="instructions" spellcheck="true"></textarea>
                                </div>

                                <div class="row no-margin-row">
                                    <div class="col-md-3">
                                        <div class="plusminus horiz custom-style quantity-card-style" style="width:100%">
                                            <button  type="button" wire:click.prevent="removeItemQty()" id="qut-button-minus">-</button>
                                            <input type="number"  name="product_quantity" id="quantity" wire:model="quantity" value="{{$quantity}}" min="1" max="99">
                                            <button  type="button"  wire:click.prevent="addItemQty()" id="qut-button-plus">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                            <button class="button fullwidth" type="submit">
                                                <i class="la la-shopping-cart"></i> {{ __('frontend.add_to_cart') }}
                                            </button>
                                    </div>
                                </div>
                            </div>
                        </form>
            </div>
                @endif

            </div>
    </div>
</div>

</div>
