<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAgent extends Model
{
    use HasFactory, HasTenantScope;

    protected $table = 'delivery_agents';

    protected $fillable = [
        'name',
        'username',
        'phone',
        'company_id',
        'source_mikoa_id',
        'destination_mikoa_id',
        'added_by',
        'is_approved',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sourceMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'source_mikoa_id');
    }

    public function destinationMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'destination_mikoa_id');
    }
}
