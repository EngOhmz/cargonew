<?php

namespace App\Models\Management;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory, HasTenantScope;

    protected $table = "drivers";

    protected $fillable = ['driver_id', 'name', 'email', 'phone', 'status', 'is_approved', 'assigned_date', 'company_id'];

    public function car(){
        return $this->hasOne('App\Models\Management\Car');
    }
    
}
