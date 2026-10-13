<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class CompanyLogoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $company = $this->resolveAuthUserCompany($user);

        if (!$company) {
            return redirect()->back()->with('error', 'Unable to resolve your company. Please contact support.');
        }

        $currentLogoUrl = null;
        $hasLogo = false;

        if ($company->logo) {
            $currentLogoUrl = asset('storage/' . $company->logo);
            $hasLogo = true;
        } else {
            $currentLogoUrl = asset('assets/images/app-logo.jpeg');
        }

        return view('access_control.company_logo', compact('currentLogoUrl', 'hasLogo', 'company'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_logo' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:4096',
            ]);

            $user = auth()->user();
            $company = $this->resolveAuthUserCompany($user);

            if (!$company) {
                return redirect()->back()->with('error', 'Unable to resolve your company. Please contact support.');
            }

            $this->ensurePublicStorageReady();

            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $logoPath = $request->file('company_logo')->store('company_logos', 'public');

            if (!$logoPath) {
                return redirect()->back()->with('error', 'Failed to upload logo. Please check file permissions.');
            }

            $company->logo = $logoPath;
            $company->save();

            \Log::info('Company logo updated successfully', [
                'user_id' => auth()->id(),
                'company_id' => $company->id,
                'logo_path' => $logoPath,
            ]);

            return redirect()->route('company-logo.index')->with('success', 'Company logo updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Company logo upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id ?? null,
            ]);

            return redirect()->back()->with('error', 'Failed to upload logo. Please try again or contact support.');
        }
    }

    public function destroy()
    {
        $user = auth()->user();
        $company = $this->resolveAuthUserCompany($user);

        if (!$company) {
            return redirect()->back()->with('error', 'Unable to resolve your company. Please contact support.');
        }

        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->logo = null;
        $company->save();

        return redirect()->route('company-logo.index')->with('success', 'Company logo removed successfully.');
    }

    /**
     * Return the authenticated user's company, creating one if the linked record is missing.
     */
    private function resolveAuthUserCompany(User $user): ?Company
    {
        if ($user->company_id) {
            $company = Company::find($user->company_id);
            if ($company) {
                return $company;
            }

            \Log::warning('User company_id points to missing company; creating replacement company.', [
                'user_id' => $user->id,
                'missing_company_id' => $user->company_id,
            ]);
        }

        $companyName = trim($user->name) !== '' ? $user->name . ' Company' : 'Company ' . $user->id;

        $company = Company::create([
            'cname' => $companyName,
            'organization' => $companyName,
            'email' => $user->email,
            'contactName' => $user->name,
            'trial_start_at' => now(),
            'trial_end_at' => now()->addDays(7),
            'is_disabled' => false,
        ]);

        $user->company_id = $company->id;
        $user->save();

        return $company;
    }

    /**
     * Ensure public disk folders and symlink exist for logo uploads.
     */
    private function ensurePublicStorageReady(): void
    {
        $companyLogosPath = storage_path('app/public/company_logos');
        if (!File::exists($companyLogosPath)) {
            File::makeDirectory($companyLogosPath, 0755, true);
        }

        $publicStorageLink = public_path('storage');
        if (!File::exists($publicStorageLink)) {
            try {
                Artisan::call('storage:link');
            } catch (\Throwable $e) {
                \Log::warning('Could not create storage symlink automatically: ' . $e->getMessage());
            }
        }
    }
}
