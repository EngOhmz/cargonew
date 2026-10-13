<?php

namespace App\Models;

use App\Traits\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class CargoRoute extends Model
{
    use HasTenantScope;

    protected $table = 'cargo_routes';

    protected $fillable = [
        'company_id',
        'from_mikoa_id',
        'to_mikoa_id',
    ];

    public function fromMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'from_mikoa_id');
    }

    public function toMikoa()
    {
        return $this->belongsTo(Mikoa::class, 'to_mikoa_id');
    }

    public function stops()
    {
        return $this->hasMany(CargoRouteStop::class, 'cargo_route_id')
            ->orderBy('stop_order');
    }

    public function getLabelAttribute(): string
    {
        $from = $this->fromMikoa->regional_name ?? '?';
        $to = $this->toMikoa->regional_name ?? '?';

        return $from . ' → ' . $to;
    }
}
