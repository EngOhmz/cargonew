<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class Mikoa extends Model
{
    use HasTenantScope;
    protected $table = 'mikoa';

    protected $fillable = ['company_id', 'regional_name'];

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }
}
