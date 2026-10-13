<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyAccess
{
    public static function isPlatformCompany(?Company $company): bool
    {
        if (!$company) {
            return false;
        }

        if (!empty($company->is_platform)) {
            return true;
        }

        return (int) $company->id === 1;
    }

    public static function isPlatformAdmin(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $roleSlugs = DB::table('users_roles')
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->where('users_roles.user_id', $user->id)
            ->pluck('roles.slug');

        if ($roleSlugs->contains('platform_admin')) {
            return true;
        }

        $company = $user->relationLoaded('company')
            ? $user->company
            : Company::find($user->company_id);

        return $roleSlugs->contains('superadmin') && self::isPlatformCompany($company);
    }

    public static function hasActiveTrial(Company $company): bool
    {
        if (!$company->trial_start_at || !$company->trial_end_at) {
            return false;
        }

        $now = now();

        return $now->gte($company->trial_start_at) && $now->lte($company->trial_end_at);
    }

    public static function hasActiveSubscription(Company $company): bool
    {
        if (!$company->subscription_start_at || !$company->subscription_end_at) {
            return false;
        }

        $now = now();

        return $now->gte($company->subscription_start_at) && $now->lte($company->subscription_end_at);
    }

    public static function canUseApplication(?Company $company): bool
    {
        if (!$company) {
            return false;
        }

        if ($company->is_disabled) {
            return false;
        }

        if (self::isPlatformCompany($company)) {
            return true;
        }

        return self::hasActiveTrial($company) || self::hasActiveSubscription($company);
    }

    public static function denialMessage(?Company $company): string
    {
        if (!$company) {
            return 'Company not found.';
        }

        if ($company->is_disabled) {
            return 'Akaunti yako imezimwa. Wasiliana na msaada.';
        }

        return 'Muda wa majaribio au usajili umekwisha. Wasiliana na msaada ili kuendelea.';
    }

    public static function userCanAccess(?User $user): bool
    {
        if (!$user || !$user->company_id) {
            return false;
        }

        return self::canUseApplication(Company::find($user->company_id));
    }

    public static function companyForUserId(?int $userId): ?Company
    {
        if (!$userId) {
            return null;
        }

        $user = User::find($userId);

        if (!$user || !$user->company_id) {
            return null;
        }

        return Company::find($user->company_id);
    }
}
