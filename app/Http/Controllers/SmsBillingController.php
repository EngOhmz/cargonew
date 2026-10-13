<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\CompanyAccess;
use App\Support\SmsUsageReport;
use Illuminate\Http\Request;

class SmsBillingController extends Controller
{
    protected function authorizePlatformAdmin(): void
    {
        if (!CompanyAccess::isPlatformAdmin(auth()->user())) {
            abort(403, 'Unauthorized access');
        }
    }

    /**
     * SMS usage overview across all registered companies.
     */
    public function index(Request $request)
    {
        $this->authorizePlatformAdmin();

        $month = SmsUsageReport::defaultMonthRange();
        $from = $request->query('from', $month['from']);
        $to = $request->query('to', $month['to']);

        $companies = Company::query()
            ->where(function ($q) {
                $q->where('is_platform', false)->orWhereNull('is_platform');
            })
            ->orderBy('cname')
            ->get();

        $smsByCompany = SmsUsageReport::countsByCompany($from, $to);
        $platformTotals = SmsUsageReport::counts(null, $from, $to);

        return view('sms_billing.index', compact(
            'companies',
            'smsByCompany',
            'platformTotals',
            'from',
            'to'
        ));
    }

    /**
     * SMS usage detail for one company.
     */
    public function show(Request $request, $id)
    {
        $this->authorizePlatformAdmin();

        $company = Company::findOrFail($id);

        if (CompanyAccess::isPlatformCompany($company)) {
            return redirect()->route('sms_billing.index')
                ->with('error', 'Platform company has no billable SMS usage.');
        }

        $month = SmsUsageReport::defaultMonthRange();
        $from = $request->query('from', $month['from']);
        $to = $request->query('to', $month['to']);

        $counts = SmsUsageReport::counts((int) $company->id, $from, $to);
        $allTimeCounts = SmsUsageReport::counts((int) $company->id);

        $logs = SmsUsageReport::usageQuery((int) $company->id, $from, $to)
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('sms_billing.show', compact(
            'company',
            'counts',
            'allTimeCounts',
            'logs',
            'from',
            'to'
        ));
    }
}
