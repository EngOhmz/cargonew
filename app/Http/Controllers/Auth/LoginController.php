<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Support\CompanyAccess;
use App\Models\Company;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        $request->merge(['is_active' => true]);

        return $request->only('email', 'password', 'is_active');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!CompanyAccess::userCanAccess($user)) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => CompanyAccess::denialMessage(Company::find($user->company_id))]);
        }

        return redirect()->intended($this->redirectPath());
    }
}
