<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;

class SetTenantContext
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            TenantContext::setFromUser(auth()->user());
            session(['tenant_company_id' => auth()->user()->company_id]);
        }

        return $next($request);
    }
}
