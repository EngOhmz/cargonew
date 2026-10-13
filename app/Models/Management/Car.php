<?php

namespace App\Models\Management;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory, HasTenantScope;

    protected $table = "cars";

    protected $fillable = ['carNumber', 'status', 'driver_id', 'closeDate', 'company_id'];
    
   
}
