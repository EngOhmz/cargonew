<?php

namespace App\Models\Customer;

use App\Models\CargoRoute;
use App\Models\Mikoa;
use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CustomerPacel extends Model
{
    use HasFactory, HasTenantScope;

 

    protected $guarded = ['id'];


    public function customer(){

        return $this->belongsTo('App\Models\Customer\Customer','customer_id');
    }

    public function cargoRoute()
    {
        return $this->belongsTo(CargoRoute::class, 'cargo_route_id');
    }

    public function dropMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'drop_mikoa_id');
    }

    public function destinations()
    {
        return $this->hasMany(CustomerPacelDestination::class, 'customer_pacel_id')
            ->orderBy('stop_order')
            ->orderBy('id');
    }

    public function hasSplitDestinations(): bool
    {
        return $this->relationLoaded('destinations')
            ? $this->destinations->count() > 1
            : $this->destinations()->count() > 1;
    }

    public function car()
    {
        return $this->belongsTo('App\Models\Management\Car', 'car_id');
    }
}
