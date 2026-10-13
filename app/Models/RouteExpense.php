<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteExpense extends Model
{
    use HasFactory;

    protected $table = 'route_expenses';

    protected $guarded = ['id'];

    public function driverRoute()
    {
        return $this->belongsTo(DriverRoute::class, 'driver_route_id');
    }

    public function car()
    {
        return $this->belongsTo(\App\Models\Management\Car::class, 'car_id');
    }

    public function driver()
    {
        return $this->belongsTo(\App\Models\Management\Driver::class, 'driver_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

