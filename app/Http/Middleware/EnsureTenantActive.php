<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Support\CompanyAccess;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTenantActive
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        TenantContext::setFromUser($user);

        if (!CompanyAccess::userCanAccess($user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => CompanyAccess::denialMessage(Company::find($user->company_id))]);
        }

        return $next($request);
    }
}
