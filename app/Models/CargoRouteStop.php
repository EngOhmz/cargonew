<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargoRouteStop extends Model
{
    protected $table = 'cargo_route_stops';

    protected $fillable = [
        'cargo_route_id',
        'mikoa_id',
        'stop_order',
        'is_final',
    ];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function cargoRoute()
    {
        return $this->belongsTo(CargoRoute::class, 'cargo_route_id');
    }

    public function mikoa()
    {
        return $this->belongsTo(Mikoa::class, 'mikoa_id');
    }
}
