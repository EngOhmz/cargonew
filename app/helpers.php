<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

// For add'active' class for activated route nav-item
function active_class($path, $active = 'active') {
    return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

// For checking activated route
function is_active_route($path) {
    return call_user_func_array('Request::is', (array)$path) ? 'true' : 'false';
}

// For add 'show' class for activated route collapse
function show_class($path) {
    return call_user_func_array('Request::is', (array)$path) ? 'show' : '';
}

function usePesapalAPI($data){


    //return App\Helpers\CustomHelper::sendRequest($data);
}

if (!function_exists('company_logo_filename')) {
    function company_logo_filename()
    {
        // Check if user is authenticated and has company_id
        if (auth()->check() && auth()->user()->company_id) {
            $company = \App\Models\Company::find(auth()->user()->company_id);
            if ($company && $company->logo) {
                return $company->logo;
            }
        }

        // Fallback to old system for backward compatibility
        $logo = null;
        if (Schema::hasTable('settings')) {
            $logo = optional(Setting::first())->site_logo;
            if ($logo && file_exists(public_path('images/' . $logo))) {
                return $logo;
            }
        }

        $metaPath = storage_path('app/company_logo.json');
        if (file_exists($metaPath)) {
            $payload = json_decode(file_get_contents($metaPath), true);
            if (!empty($payload['filename'])) {
                $candidate = $payload['filename'];
                if (file_exists(public_path('images/' . $candidate))) {
                    return $candidate;
                }
            }
        }

        return null;
    }
}

if (!function_exists('company_logo_url')) {
    function company_logo_url()
    {
        // Check if user is authenticated and has company_id
        if (auth()->check() && auth()->user()->company_id) {
            $company = \App\Models\Company::find(auth()->user()->company_id);
            if ($company && $company->logo) {
                // Logo stored in storage/app/public/company_logos
                return asset('storage/' . $company->logo);
            }
        }

        // Fallback to old system for backward compatibility
        $fileName = company_logo_filename();
        if ($fileName) {
            // Check if it's a storage path or old images path
            if (strpos($fileName, 'company_logos') !== false) {
                return asset('storage/' . $fileName);
            }
            return asset('images/' . $fileName);
        }

        // Default Madekenya brand logo
        return asset('assets/images/app-logo.jpeg');
    }
}

if (!function_exists('store_company_logo_filename')) {
    function store_company_logo_filename($fileName = null)
    {
        $metaPath = storage_path('app/company_logo.json');

        if ($fileName) {
            file_put_contents($metaPath, json_encode(['filename' => $fileName]));
        } elseif (file_exists($metaPath)) {
            unlink($metaPath);
        }
    }
}
