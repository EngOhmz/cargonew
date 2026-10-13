<?php

namespace App\Http\Controllers\Management;


use App\Http\Controllers\Controller;

use App\Models\Management\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get company_id from authenticated user
        $companyId = auth()->user()->company_id;

        $query = Driver::with('car');

        // Filter by company_id if available
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $drivers = $query->get();

        return view('management.add_driver', compact('drivers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[
            'name'=>'required',
            'email'=>'nullable|email',
        ]); 

        // Get company_id from authenticated user
        $companyId = auth()->user()->company_id;

        $driver= new Driver();

        // Generate unique 6-digit driver_id
        do {
            $driverId = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Driver::where('driver_id', $driverId)->exists());

        $driver->driver_id = $driverId;
        $driver->name=$request->input('name');
        $email = trim((string) $request->input('email', ''));
        $driver->email = $email !== '' ? $email : null;
        $driver->phone=$request->input('phone');
        $driver->status = 1;
        
        if ($companyId) {
            $driver->company_id = $companyId;
        }

        $driver->save();

        return redirect()->route('driver.index')->with('success', 'Saved Successfully. Driver ID: ' . $driverId);
    }

    /**
     * Approve a driver and create a user account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $driver = Driver::findOrFail($id);
        
        if ($driver->is_approved) {
            return redirect()->route('driver.index')->with('error', 'Driver is already approved');
        }

        // Create user for the driver
        $user = new \App\Models\User();
        $user->name = $driver->name;
        $email = trim((string) ($driver->email ?? ''));
        $user->email = $email !== '' ? $email : ($driver->driver_id . '@madekenya.local');
        $user->phone = $driver->phone;
        $user->password = \Illuminate\Support\Facades\Hash::make($driver->driver_id);
        $user->company_id = $driver->company_id;
        $user->status = 1;
        $user->is_active = true;
        $user->is_driver = 1;
        $user->save();

        // Update driver status
        $driver->is_approved = true;
        $driver->save();

        return redirect()->route('driver.index')->with('success', 'Driver approved and user account created. Login with Name and Driver ID as password.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Management\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Management\Driver  $driver
      * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $driver = Driver::find($id);

        return view('management.edit_driver', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Management\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driver $driver)
    {
        //
        $this->validate($request,[
            'name'=>'required',
            'email'=>'nullable|email',
        ]); 
        
        $data = $request->only(['name', 'phone']);
        $email = trim((string) $request->input('email', ''));
        if ($request->has('email')) {
            $data['email'] = $email !== '' ? $email : null;
        }
        $driver->update($data);

        return redirect()->route('driver.index')->with('success', 'Updated Successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Management\Driver  $driver
      * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $data=Driver::find($id);
        $data->delete();

        return redirect()->route('driver.index')->with('success', 'Deleted Successfully');
    }
}
