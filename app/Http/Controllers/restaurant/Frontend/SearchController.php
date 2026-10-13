<?php

namespace App\Http\Controllers\restaurant\Frontend;

use App\Enums\Status;
use App\Models\restaurant\Cuisine;
use App\Enums\TableStatus;
use App\Models\restaurant\Restaurant;
use App\Enums\PickupStatus;
use Illuminate\Http\Request;
use App\Enums\DeliveryStatus;
use App\Http\Services\Restaurant\RatingsService;
use App\Http\Controllers\restaurant\FrontendController;
use Illuminate\Support\Facades\DB;

class SearchController extends FrontendController
{
    public function filter( Request $request )
    {
        $restaurants = (new Restaurant())->newQuery();
        if (!blank($request->get('cuisine'))) {
            $cuisines     = Cuisine::where('slug', $request->get('cuisine'))->pluck('id')->toArray();
            $restaurants->whereHas('cuisines', function ($query) use ($cuisines) {
                $query->whereIN('cuisine_id', $cuisines);
            });
        }

        if(!blank($request->get('cuisines'))) {
            $cuisines     = Cuisine::whereIN('slug', $request->get('cuisines'))->pluck('id')->toArray();
            $restaurants->whereHas('cuisines', function ($query) use ($cuisines) {
                $query->whereIN('cuisine_id', $cuisines);
            });
        }


        if(!blank($request->get('query'))) {
            $restaurants->where('name', 'like', '%' . $request->get('query') . '%');
        }

        if(!blank($request->get('expedition'))) {
            if($request->get('expedition') == 'delivery') {
                $restaurants->where('delivery_status', DeliveryStatus::ENABLE);
            } elseif($request->get('expedition') == 'pickup') {
                $restaurants->where('pickup_status', PickupStatus::ENABLE);
            } elseif($request->get('expedition') == 'table') {
                $restaurants->where('table_status', TableStatus::ENABLE);
            }
        }
        if(!blank($request->get('lat')) && !blank($request->get('long'))) {
            $restaurants->where(['status' => 5])
            ->select(DB::raw('*, ( 6367 * acos( cos( radians('.$request->get('lat').') ) * cos( radians( `lat` ) ) * cos( radians( `long` ) - radians('.$request->get('long').') ) + sin( radians('.$request->get('lat').') ) * sin( radians( `lat` ) ) ) ) AS distance'))
                ->having('distance', '<', $request->get('distance') ?? setting('geolocation_distance_radius'))
                ->orderBy('distance');
        }
        $current_time = now()->format('H:i');
            $restaurants->where([['opening_time', '>', 'closing_time'],['opening_time', '<', $current_time]])
                ->Orwhere([['opening_time', '<', 'closing_time'],['opening_time', '<', $current_time],['closing_time', '>', $current_time]]);
        $restaurants->where(['status'=>Status::ACTIVE,'current_status'=>Status::ACTIVE,]);

        $rating        = new RatingsService();
        $totalReview   = [];
        $averageRating = [];
        $mapRestaurants = [];
        foreach($restaurants->get() as $key => $restaurant) {
            $ratingArray                    = $rating->avgRating($restaurant->id);
            $totalReview[$restaurant->id]   = $ratingArray['countUser'];
            $averageRating[$restaurant->id] = $ratingArray['avgRating'];
            $mapRestaurants[$key]['name'] = $restaurant->name;
            $mapRestaurants[$key]['slug'] = $restaurant->slug;
            $mapRestaurants[$key]['image'] = $restaurant->image;
            $mapRestaurants[$key]['logo'] = $restaurant->logo;
            $mapRestaurants[$key]['avgRating'] = $ratingArray['avgRating'];
            $mapRestaurants[$key]['countUser'] = $ratingArray['countUser'];
            $mapRestaurants[$key]['lat'] = $restaurant->lat;
            $mapRestaurants[$key]['long'] = $restaurant->long;
            $mapRestaurants[$key]['address'] = $restaurant->address;
            $mapRestaurants[$key]['url'] = route('restaurant.show',[$restaurant]);
        }
        $this->data['cuisines']  = Cuisine::orderBy('name', 'desc')->get();
        $this->data['restaurants'] = $restaurants->paginate(8)->appends(request()->query());
        $this->data['mapRestaurants']   = $mapRestaurants;
        $this->data['totalReview']   = $totalReview;
        $this->data['averageRating'] = $averageRating;
        return view('restaurant.frontend.search', $this->data);
    }


}
