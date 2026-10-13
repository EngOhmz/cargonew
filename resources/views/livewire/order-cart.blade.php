<div>

    <div class="col-md-12">
        {{-- <p class="shop-info">{{ __('frontend.your_order_from').' '.Str::limit($restaurant->name,26) }}</p> --}}
    </div>
    
    
    <div class="col-md-12 item-list">
        @if(!blank($carts))
            @foreach($carts['items'] as $key => $content)
                <table class="table cart-table">
                    <tr>
                        <td>
                            <button class="cart-item-delete-button" wire:click.prevent="removeItem('{{$key}}')"><i class="las la-trash"></i></button>
                        </td>
                        <td class="product-style">
                            {{ $content['name'] }}
                        </td>
                        <td class="style float-right">
                            {{ $content['totalPrice'] }}
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="variation-option-style">
                            @if(isset($content['variation']['name']) && isset($content['variation']['price']))
                                <b>{{ $content['variation']['name'] }}</b>
                            @endif
                            @if(!blank($content['options']))
                                <br>
                                @foreach ($content['options'] as $option)
                                    <span>+ {{ $option['name'] }}</span><br>
                                @endforeach
                            @endif
                        </td>
    
                        <td class="float-right">
                            <div class="plusminus horiz custom-style" wire:key="{{ $key }}">
                                <button wire:click.prevent="removeItemQty('{{$key}}')"><i class="las la-minus"></i></button>
                                <input type="number" wire:model="carts.items.{{$key}}.qty"  id="carts.items.{{$key}}.qty" wire:keyup="changeEvent('{{$key}}')" value="{{ $content['qty'] }}" min="1" max="99">
                                <button  wire:click.prevent="addItemQty('{{$key}}')"><i class="las la-plus"></i></button>
                            </div>
                        </td>
                    </tr>
                </table>
            @endforeach
        @endif
    </div>
    
    <div class="col-md-12">
        <hr class="hr-cart-style">
        @if (Schema::hasColumn('coupons', 'slug'))
            <div class=" apply-coupon d-flex justify-content-between">
                <input class="" wire:model="coupon" placeholder="{{ __('frontend.apply_coupon') }}" type="text">
                <button wire:click.prevent="addCoupon()"> {{__('frontend.apply')}}</button>
            </div>
        @endif
        <span class="coupon-message">{{$msg}}</span>
    
        <table class="table cart-table subtotal">
            <tr>
                <td>{{ __('frontend.subtotal') }}</td>
                
                <td class="float-right">{{ $subTotalAmount }}</td>
            </tr>
            <tr>
                <td>{{ __('frontend.discount') }}</td>
                
                <td class="float-right">{{ $discountAmount }}</td>
            </tr>
            <tr>
                <td>{{ __('frontend.delivery_charge') }}</td>
                <td class="float-right">{{$delivery_charge }}</td>
            </tr>
            <tr>
                <td class="bold">{{ __('frontend.total') }}</td>
                <td class="bold float-right">{{ $totalPayAmount }}</td>
            </tr>
        </table>
        <a href="{{ route('checkout.index') }}" class="btn @if(!blank($carts) && !blank($carts['items'])) btn-checkout @else btn-checkout-disabled @endif " @if(!blank($carts) && !blank($carts['items'])) onclick="return true;" @else onclick="return false;" @endif >{{ __('frontend.go_to_checkout') }}</a>
    </div>
    
    
    </div>
    