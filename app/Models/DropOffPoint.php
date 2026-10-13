<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropOffPoint extends Model
{
    use HasFactory, HasTenantScope;

    protected $guarded = ['id'];

    public function car()
    {
        return $this->belongsTo('App\Models\Management\Car', 'car_id');
    }

    public function driver()
    {
        return $this->belongsTo('App\Models\Management\Driver', 'driver_id');
    }

    public function driverRoute()
    {
        return $this->belongsTo('App\Models\DriverRoute', 'driver_route_id');
    }

    public function mikoa()
    {
        return $this->belongsTo(Mikoa::class, 'mikoa_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}


