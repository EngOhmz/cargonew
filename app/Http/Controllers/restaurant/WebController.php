<?php

namespace App\Http\Controllers\restaurant;

use Illuminate\Support\Facades\Schema;
use App\Models\Coupon;
use App\Models\restaurant\Cuisine;
use App\Models\Discount;
use App\Models\restaurant\Restaurant;
use App\Enums\CuisinesStatus;
use App\Enums\RestaurantStatus;
use App\Http\Controllers\restaurant\FrontendController;


class WebController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
        if (file_exists(storage_path('installed'))) {
            $this->data['site_title'] = setting('site_name');
        } else {
            return redirect('/install');
        }
    }

    public function index()
    {
        $current_time = now()->format('H:i');
        $today = date('Y-m-d h:i:s');
        $this->data['vouchers'] = [];

        if (Schema::hasColumn('coupons', 'slug')) {

            $vouchers = Coupon::whereDate('to_date', '>=', $today)
                ->whereDate('from_date', '<=', $today)
                ->where('restaurant_id', '!=', 0)
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
                    $this->data['vouchers'] = pluck($data, 'slug', 'restaurant_id');
                }
            }
        }

        $this->data['cuisines'] = Cuisine::with('restaurants.orders')->where('status', CuisinesStatus::ACTIVE)->get();
        $this->data['bestSellingRestaurants']   = Restaurant::leftJoin('orders', 'restaurant_id', '=', 'restaurants.id')
            ->where(['restaurants.status' => RestaurantStatus::ACTIVE, 'restaurants.current_status' => RestaurantStatus::ACTIVE,])
            ->where([['opening_time', '>', 'closing_time'], ['opening_time', '<', $current_time]])
            ->Orwhere([['opening_time', '<', 'closing_time'], ['opening_time', '<', $current_time], ['closing_time', '>', $current_time]])
            ->select('restaurants.id', 'restaurants.name', 'restaurants.slug', 'restaurants.delivery_charge')
            ->selectRaw('count("orders.id") as orders_count')
            ->groupBy('restaurants.id')
            ->orderBy('orders_count', 'desc')
            ->take(10)
            ->get();

        $this->data['bestSellingCuisines'] = collect($this->data['cuisines'])->map(function ($cuisine) {
            $cuisine['order_counter']  = 0;
            foreach ($cuisine->restaurants as $key => $restaurant) {
                $cuisine['order_counter']  += $restaurant->orders->count();
            }
            return $cuisine;
        });

        $this->data['bestSellingCuisines'] = $this->data['bestSellingCuisines']->sortByDesc('order_counter')->take(6);
        $this->data['top_cuisines'] = $this->data['cuisines']->take(3);

        return view('restaurant.frontend.home-page', $this->data);
    }
}
