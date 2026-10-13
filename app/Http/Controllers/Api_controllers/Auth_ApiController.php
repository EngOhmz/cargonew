<?php

namespace App\Http\Controllers\Api_controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Support\CompanyAccess;
use Illuminate\Http\Request;
use App\Models\TechTransfer\Respondant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Throwable\Exception;

class Auth_ApiController extends Controller
{
   
    /**
     * Login function.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //validation 
        $rules = [
            'password'=>'required'
        ];
        
        // Support both 'email' (old) and 'username' (new)
        $loginField = $request->has('username') ? $request->username : $request->email;
        
        if(!$loginField) {
            return response()->json(['success'=>false,'error'=>true,'message'=>'username or email is required'], 200);
        }

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $massage = $validator->errors()->first();
            $response=['success'=>false,'error'=>true,'message'=>$massage];
            return response()->json($response,200);

        }
        
        // Try to find user by email first, then by name (username)
        $user = User::where('email', $loginField)
                    ->orWhere('name', $loginField)
                    ->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'message' => 'Akaunti yako imezimwa. Wasiliana na msaada.',
                ], 200);
            }

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

            // Include company_id in response for SaaS multi-tenancy
            $userData = $user->toArray();
            $userData['company_id'] = $user->company_id; // Ensure company_id is included
            
            // If user is a driver, find their 6-digit driver_id from the drivers table
            if ($user->is_driver == 1) {
                $driver = \App\Models\Management\Driver::where('company_id', $user->company_id)
                    ->where(function($q) use ($user) {
                        $q->where('email', $user->email)
                          ->orWhere('name', $user->name);
                    })->first();
                
                if ($driver) {
                    $userData['drivers_id'] = $driver->id;
                }
            }

            // Include delivery agent status and find their ID if applicable
            $userData['is_delivery_agent'] = (int)$user->is_delivery_agent;
            if ($user->is_delivery_agent == 1) {
                // Always expose destination label key for delivery agents; value is null when unset or unlinked.
                $userData['delivery_agent_destination_name'] = null;

                $agent = \App\Models\DeliveryAgent::with(['destinationMikoa', 'sourceMikoa'])
                    ->where('company_id', $user->company_id)
                    ->where(function ($q) use ($user) {
                        $q->where('username', $user->email)
                            ->orWhere('name', $user->name);
                    })->first();

                if ($agent) {
                    $userData['delivery_agent_id'] = $agent->id;
                    // Region label only (e.g. "Geita"); mikoa id is not exposed. Null when no destination chosen.
                    if ($agent->destinationMikoa) {
                        $userData['delivery_agent_destination_name'] = $agent->destinationMikoa->regional_name;
                    }
                }
            }
            
            $response=['success'=>true,'error'=>false,'message'=>'User login successfully','user'=>$userData];

            return response()->json($response,200);
        }
        else{
            $response=['success'=>false,'error'=>true,'message'=>'incorrect username/email or password'];
            return response()->json($response,200);
        }
    }

    public function register_as()
    {
        $roles = Role::all()->whereNotIn('slug', 'superAdmin');
        return response()->json($roles);
    }

    public function user_email(String $email)
    {
        $emailFind = User::all()->where('email', $email)->first();
        if($emailFind){
            $response=['success'=>false,'error'=>true, 'message'=>'Email exists'];
            return response()->json($response,200);
        }
        else{
            $response=['success'=>true,'error'=>false, 'message'=>'Proceed'];
            return response()->json($response,200);
        }
        // $roles = Role::all()->whereNotIn('slug', 'superAdmin');
        
    }

    /**
     * Logout function.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Validation
        $rules = [
            'user_id' => 'required|integer'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        
        if($validator->fails()) {
            $message = $validator->errors()->first('user_id');
            $response = ['success' => false, 'error' => true, 'message' => $message];
            return response()->json($response, 200);
        }
        
        // Verify user exists
        $user = User::find($request->user_id);
        
        if($user) {
            // If using token-based authentication, you can revoke tokens here
            // For example, with Laravel Sanctum:
            // $user->tokens()->delete();
            
            $response = [
                'success' => true,
                'error' => false,
                'message' => 'User logged out successfully'
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'error' => true,
                'message' => 'User not found'
            ];
            return response()->json($response, 200);
        }
    }

    

    

    // public function get_countries()
    // {
    //     $countries = DB::table('countries')->get();
    //     return response()->json($countries);
    // }


   
}