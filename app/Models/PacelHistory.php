<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacelHistory extends Model
{
    use HasFactory, HasTenantScope;

    protected $guarded = ['id'];


    public function customerPacel(){

        return $this->belogsTo('App\Models\Customer\Customer','pacel_id');
    }

    public function car()
    {
        return $this->belongsTo(\App\Models\Management\Car::class, 'car_id');
    }
}
