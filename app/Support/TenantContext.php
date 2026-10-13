<?php

namespace App\Support;

use App\Models\User;

class TenantContext
{
    protected static ?int $companyId = null;

    protected static bool $scopeDisabled = false;

    public static function reset(): void
    {
        self::$companyId = null;
        self::$scopeDisabled = false;
    }

    public static function setFromUser(?User $user): void
    {
        self::reset();

        if (!$user || !$user->company_id) {
            return;
        }

        if (CompanyAccess::isPlatformAdmin($user)) {
            self::$scopeDisabled = true;

            return;
        }

        self::$companyId = (int) $user->company_id;
    }

    public static function setCompanyId(?int $companyId): void
    {
        self::$companyId = $companyId;
        self::$scopeDisabled = false;
    }

    public static function disableScope(): void
    {
        self::$scopeDisabled = true;
    }

    public static function shouldApplyScope(): bool
    {
        if (self::$scopeDisabled) {
            return false;
        }

        if (auth()->check() && CompanyAccess::isPlatformAdmin(auth()->user())) {
            return false;
        }

        return self::resolveCompanyId() !== null;
    }

    public static function resolveCompanyId(): ?int
    {
        if (self::$scopeDisabled) {
            return null;
        }

        if (self::$companyId !== null) {
            return self::$companyId;
        }

        if (auth()->check() && auth()->user()->company_id) {
            if (CompanyAccess::isPlatformAdmin(auth()->user())) {
                return null;
            }

            return (int) auth()->user()->company_id;
        }

        return null;
    }
}
