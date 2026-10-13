<div>

    <div id="listing-product-list">
        @foreach($categories_products as $categories_product_key => $categories_product)
            <div id="listing-product-list-category-{{$categories_product_key}}" class="listing-nav-container-body">
                <div class="container-fluid custom-container">
                    <div class="col-lg-12 col-md-12">
                        <h2 class="category-style">{{ $categories[$categories_product_key]->name }}</h2>
                        {{-- <p>{{ Str::limit(strip_tags($categories[$categories_product_key]->description), 130) }}</p> --}}
                        <div class="#">
                            <div class="row">
                                @if(!blank($categories_product))
                                    @foreach($categories_product as $menu_item)
                                        <div class="col-md-6 padding-left-0">
                                            <div class="style-2">
                                                <div class="tabs-container border-radius">
                                                    <div class="tab-content food-price-tab" id="tab1a">
                                                        <div class="product-description-section">
                                                            <a href="#variation-{{$menu_item['id']}}" wire:click.prevent="addToCartModal({{$menu_item['id']}})">
                                                                <p class="product-name-style">{{Str::limit($menu_item['name'],20)}}</p>
                                                                <p class="food-price-p with-color">{!! Str::limit(strip_tags($menu_item['description']),44)!!}</p>
                                                                @if($menu_item['discount_price'] > 0)
                                                                    <p class="food-price-p custom-design">
                                                                        <span class="discount-price">{{setting('currency_name')}} {{$menu_item['unit_price']}}</span>
                                                                        {{setting('currency_name')}} {{$menu_item['unit_price'] - $menu_item['discount_price']}}
                                                                    </p>
                                                                @else
                                                                    <p class="food-price-p custom-design">
                                                                        {{setting('currency_name')}} {{$menu_item['unit_price'] - $menu_item['discount_price']}}
                                                                    </p>
                                                                @endif
                                                            </a>
                                                        </div>

                                                        <div class="product-image-section">
                                                            <a href="#variation-{{$menu_item['id']}}" wire:click.prevent="addToCartModal({{$menu_item['id']}})">
                                                                <img class="menu-img" src="{{ $menu_item['image'] }}" alt="">
                                                                <i class="fa fa-plus menu-cart"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if(!blank($other_products))
            <div id="listing-product-list-category-{{ __('other') }}" class="listing-nav-container-body">
                <div class="container-fluid custom-container">
                    <div class="col-lg-12 col-md-12">
                        <h2 class="category-style">{{ __('frontend.other') }}</h2>
                        <p>{{ __('frontend.you_can_select_other_product') }}</p>
                        <div class="#">
                            <div class="row">
                                @foreach($other_products as $other_product)
                                    <div class="col-md-6 padding-left-0">
                                        <div class="style-2">
                                            <div class="tabs-container border-radius">
                                                <div class="tab-content food-price-tab" id="tab1a">
                                                    <div class="product-description-section">
                                                        <a href="#variation-{{$other_product['id']}}" wire:click.prevent="addToCartModal({{$other_product['id']}})">
                                                            <p class="product-name-style">{{Str::limit($other_product['name'],20)}}</p>
                                                            <p class="food-price-p with-color">{!! Str::limit(strip_tags($other_product['description']),44)!!}</p>
                                                            @if($other_product['discount_price'] > 0)
                                                                <p class="food-price-p custom-design">
                                                                    <span class="discount-price"> {{$other_product['unit_price']}}</span>
                                                                   {{$other_product['unit_price'] - $other_product['discount_price']}}
                                                                </p>
                                                            @else
                                                                <p class="food-price-p custom-design">
                                                                   {{$other_product['unit_price'] - $other_product['discount_price']}}
                                                                </p>
                                                            @endif
                                                        </a>
                                                    </div>

                                                    <div class="product-image-section">
                                                        <a href="#variation-{{$other_product['id']}}" wire:click.prevent="addToCartModal({{$other_product['id']}})">
                                                            <img class="menu-img" src="{{ $other_product['image'] }}" alt="">
                                                            <i class="fa fa-plus menu-cart"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
