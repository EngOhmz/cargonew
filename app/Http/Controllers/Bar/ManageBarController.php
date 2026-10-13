<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use App\Models\Bar\Bar;
use App\Models\Inventory\Location;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class ManageBarController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
           $index=Bar::all();
           $location=Location::all();
            return view('bar.index', compact('index','location'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id']=auth()->user()->id;
        $bar= Bar::create($data);
        Toastr::success('New Bar Created Successfully','Success');
        return redirect(route('manage_bar.index'));
    }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $data=Bar::find($id);
        $location=Location::all();
         return view('bar.index', compact('data','location','id'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $bar=Bar::find($id);

        $data = $request->all();
        $bar->update($data);
        Toastr::success('Bar Updated Successfully','Success');
        return redirect(route('manage_bar.index'));
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Bar::find($id)->delete();
        Toastr::success('Bar Deleted Successfully','Success');
        return redirect(route('manage_bar.index'));
    }

 
}
