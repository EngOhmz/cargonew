<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterEmasuiteController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Allow both guests and authenticated users to access registration
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // No need to pass companies anymore - company will be auto-created
        return view('auth.register_emasuite');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Company name from registration form
        $companyName = trim($data['name']);
        
        // Set trial period: 7 days from now
        $trialStartAt = now();
        $trialEndAt = now()->addDays(7);
        
        $company = Company::create([
            'cname' => $companyName,
            'organization' => $companyName,
            'email' => $data['email'],
            'contactName' => $companyName,
            'app_name' => 'Madekenya',
            'trial_start_at' => $trialStartAt,
            'trial_end_at' => $trialEndAt,
            'is_disabled' => false,
        ]);
        
        $companyId = $company->id;
            
        $user = User::create([
            'name' => $companyName,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $companyId,
            'status' => 1,
            'is_active' => 1,
            'added_by' => null, // No added_by since this is a new company registration
        ]);

            // Create superadmin role for this company
        // This will be the first role for the company
            $superadminRole = Role::create([
                'slug' => 'superadmin',
                'company_id' => $companyId,
                'added_by' => $user->id,
            ]);

        // Attach superadmin role to the user
        $user->roles()->attach($superadminRole->id);

        $permissionIds = \App\Models\Permission::pluck('id')->all();
        if (!empty($permissionIds)) {
            $superadminRole->permissions()->sync($permissionIds);
        }

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Always auto-login the newly registered user
        // This ensures the user is logged in after registration
        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // Redirect to home/dashboard after successful registration and login
        return $request->wantsJson()
                    ? new \Illuminate\Http\JsonResponse([], 201)
                    : redirect($this->redirectPath())->with('success', 'Registration successful! You are now logged in as superadmin.');
    }
}

