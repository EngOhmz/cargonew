<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\User;
use App\Support\CompanyAccess;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;

class EnsureApiTenantActive
{
    protected array $except = [
        'api/login',
        'api/logout',
        'api/mikoa',
        'api/mobile_settings',
        'api/mobile_settings/update',
        'api/pesapalResonse',
        'api/driver/save',
        'api/user_email',
    ];

    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        foreach ($this->except as $pattern) {
            if ($path === $pattern || str_starts_with($path, $pattern . '/')) {
                return $next($request);
            }
        }

        $userId = $request->input('user_id') ?? $request->query('user_id');
        $companyId = $request->input('company_id') ?? $request->query('company_id');

        if ($userId) {
            $user = User::find($userId);

            if ($user) {
                TenantContext::setFromUser($user);

                if (!CompanyAccess::userCanAccess($user)) {
                    $company = Company::find($user->company_id);

                    return response()->json([
                        'success' => false,
                        'error' => true,
                        'message' => CompanyAccess::denialMessage($company),
                        'company_disabled' => $company ? (bool) $company->is_disabled : false,
                        'subscription_expired' => $company ? !CompanyAccess::canUseApplication($company) : true,
                    ], 200);
                }
            }
        } elseif ($companyId) {
            $company = Company::find($companyId);

            if ($company && !CompanyAccess::canUseApplication($company)) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => CompanyAccess::denialMessage($company),
                    'company_disabled' => (bool) $company->is_disabled,
                    'subscription_expired' => true,
                ], 200);
            }

            TenantContext::setCompanyId((int) $companyId);
        }

        return $next($request);
    }
}
