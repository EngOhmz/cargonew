<?php

namespace App\Models\Customer;

use App\Models\CargoRoute;
use App\Models\Mikoa;
use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class CustomerPacelDestination extends Model
{
    use HasTenantScope;

    protected $guarded = ['id'];

    public function parcel()
    {
        return $this->belongsTo(CustomerPacel::class, 'customer_pacel_id');
    }

    public function cargoRoute()
    {
        return $this->belongsTo(CargoRoute::class, 'cargo_route_id');
    }

    public function dropMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'drop_mikoa_id');
    }

    public function remainingOnTruck(): int
    {
        return max(0, (int) $this->idadi_kupakia);
    }
}
