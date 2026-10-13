<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParcelCarAssignment extends Model
{
    use HasFactory, HasTenantScope;

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer\Customer::class, 'customer_id');
    }

    public function car()
    {
        return $this->belongsTo(\App\Models\Management\Car::class, 'car_id');
    }

    public function parcel()
    {
        return $this->belongsTo(\App\Models\Customer\CustomerPacel::class, 'pacel_id');
    }
}
