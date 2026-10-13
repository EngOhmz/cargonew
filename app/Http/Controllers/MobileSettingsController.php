<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MobileSettingsController extends Controller
{
    /**
     * Display the mobile settings form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $company = Company::find($companyId);
        
        return view('mobile_settings.index', compact('company'));
    }

    /**
     * Update mobile app settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $companyId = auth()->user()->company_id;
        $company = Company::findOrFail($companyId);

        $data = [
            'app_name' => $request->input('app_name'),
        ];

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            // Delete old logo if exists
            if ($company->app_logo && Storage::disk('public')->exists($company->app_logo)) {
                Storage::disk('public')->delete($company->app_logo);
            }

            // Store new logo
            $logoPath = $request->file('app_logo')->store('app_logos', 'public');
            $data['app_logo'] = $logoPath;
        }

        $company->update($data);

        return redirect()->route('mobile_settings.index')
            ->with('success', 'Mobile app settings updated successfully.');
    }

    /**
     * Get mobile app settings via API.
     * Uses user_id from request to get the logged-in user's company_id
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings(Request $request)
    {
        // Get user_id from query parameters (GET request) or request body
        // For GET requests, use query() method; for POST, use input()
        // Also check all() to catch any format
        $userId = $request->query('user_id') 
                ?? $request->input('user_id') 
                ?? $request->get('user_id')
                ?? ($request->all()['user_id'] ?? null);
        
        // Validate user_id - check if it exists and is valid
        if (!$userId) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'The user_id field is required.',
                'errors' => ['user_id' => ['The user_id field is required.']]
            ], 200)->header('Content-Type', 'application/json');
        }
        
        // Validate it's an integer
        if (!is_numeric($userId)) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'The user_id must be an integer.',
                'errors' => ['user_id' => ['The user_id must be an integer.']]
            ], 200)->header('Content-Type', 'application/json');
        }

        // Convert to integer
        $userId = (int)$userId;
        
        // Check if user exists
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'User not found. Please check user_id.'
            ], 200);
        }

        // Check if user has company_id
        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'User has no company assigned. Please contact administrator.'
            ], 200);
        }

        // Get company using the user's company_id
        $companyId = $user->company_id;
        $company = Company::find($companyId);
        
        if (!$company) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Company not found for this user'
            ], 200);
        }

        // Build logo URL if exists
        $logoUrl = null;
        if ($company->app_logo) {
            $logoUrl = url('storage/' . $company->app_logo);
        }

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Mobile app settings retrieved successfully',
            'data' => [
                'app_name' => $company->app_name ?: $company->cname ?: 'Madekenya',
                'app_logo' => $logoUrl,
                'company_name' => $company->cname,
                'user_id' => (int)$user->id,
                'company_id' => (int)$company->id,
            ]
        ], 200)->header('Content-Type', 'application/json');
    }

    /**
     * Update mobile app settings via API.
     * Uses user_id from request to get the logged-in user's company_id
     * Then updates app_name and app_logo for that company
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'app_name' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Get user_id from request (logged-in user)
        $userId = $request->input('user_id');
        
        // Check if user exists
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'User not found. Please check user_id.'
            ], 200);
        }

        // Check if user has company_id
        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'User has no company assigned. Please contact administrator.'
            ], 200);
        }

        // Get company using the user's company_id
        $companyId = $user->company_id;
        $company = Company::find($companyId);
        
        if (!$company) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Company not found for this user'
            ], 200);
        }

        // Prepare data to update
        $data = [];
        
        // Update app_name if provided
        if ($request->has('app_name')) {
            $data['app_name'] = $request->input('app_name');
        }

        // Handle logo upload if provided
        if ($request->hasFile('app_logo')) {
            // Delete old logo if exists
            if ($company->app_logo && Storage::disk('public')->exists($company->app_logo)) {
                Storage::disk('public')->delete($company->app_logo);
            }

            // Store new logo
            $logoPath = $request->file('app_logo')->store('app_logos', 'public');
            $data['app_logo'] = $logoPath;
        }

        // Update company settings
        if (!empty($data)) {
            $company->update($data);
        }

        // Build logo URL if exists
        $logoUrl = null;
        if ($company->app_logo) {
            $logoUrl = asset('storage/' . $company->app_logo);
        }

        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'Mobile app settings updated successfully',
            'data' => [
                'app_name' => $company->app_name ?: $company->cname ?: 'Madekenya',
                'app_logo' => $logoUrl,
                'company_name' => $company->cname,
                'user_id' => $user->id,
                'company_id' => $company->id,
            ]
        ], 200);
    }
}
