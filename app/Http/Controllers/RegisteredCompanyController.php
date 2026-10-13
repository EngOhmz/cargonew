<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Support\CompanyAccess;
use Illuminate\Http\Request;

class RegisteredCompanyController extends Controller
{
    protected function authorizePlatformAdmin(): void
    {
        if (!CompanyAccess::isPlatformAdmin(auth()->user())) {
            abort(403, 'Unauthorized access');
        }
    }

    public function index()
    {
        $this->authorizePlatformAdmin();

        $companies = Company::with('users')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('registered_companies.index', compact('companies'));
    }

    public function edit($id)
    {
        $this->authorizePlatformAdmin();

        $company = Company::with('users')->findOrFail($id);

        return view('registered_companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizePlatformAdmin();

        $company = Company::findOrFail($id);

        if (CompanyAccess::isPlatformCompany($company)) {
            return redirect()->route('registered_companies.index')
                ->with('error', 'Platform company settings cannot be edited here.');
        }

        $request->validate([
            'trial_start_at' => 'nullable|date',
            'trial_end_at' => 'nullable|date|after_or_equal:trial_start_at',
            'subscription_start_at' => 'nullable|date',
            'subscription_end_at' => 'nullable|date|after_or_equal:subscription_start_at',
            'is_disabled' => 'boolean',
            'sms_sender_id' => 'nullable|string|max:11',
        ]);

        if ($request->has('extend_trial_days')) {
            $days = (int) $request->extend_trial_days;
            if ($days > 0) {
                $company->trial_end_at = now()->addDays($days);
                if (!$company->trial_start_at) {
                    $company->trial_start_at = now();
                }
            }
        }

        if ($request->filled('subscription_start_at')) {
            $company->subscription_start_at = $request->subscription_start_at;
        } elseif ($request->has('subscription_start_at')) {
            $company->subscription_start_at = null;
        }
        if ($request->filled('subscription_end_at')) {
            $company->subscription_end_at = $request->subscription_end_at;
        } elseif ($request->has('subscription_end_at')) {
            $company->subscription_end_at = null;
        }

        if ($request->filled('trial_start_at')) {
            $company->trial_start_at = $request->trial_start_at;
        } elseif ($request->has('trial_start_at')) {
            $company->trial_start_at = null;
        }
        if ($request->filled('trial_end_at')) {
            $company->trial_end_at = $request->trial_end_at;
        } elseif ($request->has('trial_end_at')) {
            $company->trial_end_at = null;
        }

        $company->is_disabled = $request->has('is_disabled') && $request->is_disabled ? true : false;

        $senderId = trim((string) $request->input('sms_sender_id', ''));
        $company->sms_sender_id = $senderId !== '' ? $senderId : null;

        $company->save();

        return redirect()->route('registered_companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function toggleDisabled($id)
    {
        $this->authorizePlatformAdmin();

        $company = Company::findOrFail($id);

        if (CompanyAccess::isPlatformCompany($company)) {
            return redirect()->route('registered_companies.index')
                ->with('error', 'Platform company cannot be disabled.');
        }

        $company->is_disabled = !$company->is_disabled;
        $company->save();

        $status = $company->is_disabled ? 'disabled' : 'enabled';

        return redirect()->route('registered_companies.index')
            ->with('success', "Company has been {$status} successfully.");
    }
}
