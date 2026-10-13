<?php

namespace App\Http\Controllers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');

        //return view('dashboard.dashboard1');
    }

    public function schedule(Schedule $schedule){
        $schedule->call(function () {
            DB::table('cars')->whereNotNull('closeDate')->whereRaw('closeDate < now()')->update(['status' => '1']);
        })->everyMinute();
        
    }
    
    
     public function showChangePswd(){
        return view('auth.change_password');
    }
    

    public function changePswd(Request $request){
        if(!Hash::check($request->get('current-password'), Auth::user()->password)){
            //check if password matches

            return back()->with('error', 'Curent Password does not match with Old Password');
        }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
                //current password and new password are the same
                return back()->with('error', 'New password can not be the same as your old password change to new one');
        }

        $this->validate($request, [
            'current-password' => 'required',
            'new-password' => 'required|string|min:8|confirmed'
        ]);

        // update password

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->get('new-password'))
        ]);

        return back()->with('success', 'Password changed successfully');
    }
}
