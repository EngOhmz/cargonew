<?php

namespace App\Http\Controllers\restaurant;

use App\Models\restaurant\Order;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\restaurant\MenuItem;
use App\Enums\OrderStatus;
use App\Models\restaurant\Restaurant;
use App\Enums\RatingStatus;
use App\Enums\MenuItemStatus;
use App\Models\restaurant\RestaurantRating;
use Sopamo\LaravelFilepond\Filepond;
use App\Http\Requests\RatingsRequest;
use App\Http\Services\restaurant\RatingsService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redirect;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\restaurant\FrontendController;

class RestaurantController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['site_title'] = 'Frontend';
    }

    public function show(Filepond $filepond)
    {

       
        if (auth()->user()) {
            $order = Order::where([
                'status'        => OrderStatus::COMPLETED,
                'user_id'       => auth()->user()->id
            ])->get();
        } else {
            $order = [];
        }



        $products = MenuItem::with('categories')->with('media')->with('variations')->with('options')->where('status', MenuItemStatus::ACTIVE)->get();
        foreach ($products as $product) {
            $product_categories = $product->categories;
            if (!blank($product_categories)) {
                foreach ($product_categories as $product_category) {
                    $categories[$product_category->id]            = $product_category;
                    $categories_products[$product_category->id][] = $product;
                }
            } else {
                $other_products[] = $product;
            }
        }

        if (Schema::hasColumn('coupons', 'slug')) {
            $today = date('Y-m-d h:i:s');
            $this->data['vouchers'] = [];
            $vouchers = Coupon::whereDate('to_date', '>', $today)
                ->whereDate('from_date', '<', $today)
                ->where('limit', '>', 0)->get();
            if (!blank($vouchers)) {
                $data = [];
                foreach ($vouchers as $voucher) {
                    $total_used = Discount::where('coupon_id', $voucher->id)->where('status', \App\Enums\DiscountStatus::ACTIVE)->count();
                    if ($total_used < $voucher->limit) {
                        $data[] = $voucher;
                    }
                }
                if (!blank($data)) {
                    $this->data['vouchers']         = pluck($data, 'obj', 'restaurant_id');
                }
            }
        }
        
        $this->data['categories']          = isset($categories) ? $categories : 0;
        $this->data['categories_products'] = isset($categories_products) ? $categories_products: 0;
        $this->data['other_products']      = isset($other_products) ? $other_products: 0;
        $this->data['order_status']        = isset($order) ? !blank($order): 0;

        return view('restaurant.frontend.restaurant.show', $this->data);
    }

    private function qrCode(Restaurant $restaurant, Filepond $filepond)
    {
        if ($restaurant) {
            $image = QrCode::size(480)->format('png')->margin(1)->encoding('UTF-8');
            if (isset($restaurant->qrCode)) {
                if (isset($restaurant->qrCode->color)) {
                    $colors = explode(",", $restaurant->qrCode->color);
                } else {
                    $colors = [0, 0, 0];
                }

                if (isset($restaurant->qrCode->background_color)) {
                    $bgColors = explode(",", $restaurant->qrCode->background_color);
                } else {
                    $bgColors = [255, 255, 255];
                }

                $image = $image->style(isset($restaurant->qrCode->style) ? $restaurant->qrCode->style : 'square')->eye(isset($restaurant->qrCode->eye_style) ? $restaurant->qrCode->eye_style : 'square')->color(intval($colors[0]), intval($colors[1]), intval($colors[2]))->backgroundColor(intval($bgColors[0]), intval($bgColors[1]), intval($bgColors[2]));

                if ($restaurant->qrCode->mode == 'logo' && !blank($restaurant->qrCode->qrcode_logo)) {
                    $path  = $filepond->getPathFromServerId($restaurant->qrCode->qrcode_logo);
                    $image = $image->merge($path, .2, true);
                }
            }

            $image = $image->generate(route('restaurant.show', $restaurant->slug));
            return base64_encode($image);

        }
    }

    public function Ratings(RatingsRequest $request)
    {
        $restaurantRating = RestaurantRating::where([
            'user_id'       => auth()->user()->id,
            'restaurant_id' => $request->restaurant_id
        ])->first();
        if ($restaurantRating) {
            $restaurantRating->rating = $request->rating;
            $restaurantRating->review = $request->review;
            $restaurantRating->save();
            return Redirect::back()->withSuccess('The Data Update Successfully');
        } else {
            $restaurantRating                = new RestaurantRating;
            $restaurantRating->user_id       = auth()->id();
            $restaurantRating->restaurant_id = $request->restaurant_id;
            $restaurantRating->rating        = $request->rating;
            $restaurantRating->review        = $request->review;
            $restaurantRating->status        = $request->status;
            $restaurantRating->save();
            return Redirect::back()->withSuccess('The Data Inserted Successfully');
        }
    }
}
