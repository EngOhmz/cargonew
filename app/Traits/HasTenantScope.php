<?php

namespace App\Traits;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;

trait HasTenantScope
{
    protected static function bootHasTenantScope()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (!TenantContext::shouldApplyScope()) {
                return;
            }

            $companyId = TenantContext::resolveCompanyId();
            if ($companyId === null) {
                return;
            }

            $table = $builder->getModel()->getTable();
            $builder->where($table . '.company_id', $companyId);
        });

        static::creating(function ($model) {
            if ($model->company_id) {
                return;
            }

            $companyId = TenantContext::resolveCompanyId();
            if ($companyId !== null) {
                $model->company_id = $companyId;

                return;
            }

            if (auth()->check() && auth()->user()->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope('tenant');
    }
}
