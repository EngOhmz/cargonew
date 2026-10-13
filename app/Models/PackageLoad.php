<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageLoad extends Model
{
    use HasFactory, HasTenantScope;

    protected $table = 'package_loads';

    protected $fillable = [
         'user_id','car_id','pacel_id','idadi','price','company_id'
    ];
}
