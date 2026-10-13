<?php

namespace App\Http\Controllers\restaurant;

use App\Http\Controllers\Controller;
use App\Models\restaurant\Restaurant;
use App\Models\Inventory\Location;
use App\Models\restaurant\Table;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;


class TableController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
           $index=Table::all();
           $location=Restaurant::all();
            return view('restaurant.table.index', compact('index','location'));

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
        $table= Table::create($data);
        Toastr::success('New Table Created Successfully','Success');
        return redirect(route('tables.index'));
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

        $data=Table::find($id);
        $location=Restaurant::all();
         return view('restaurant.table.index', compact('data','location','id'));

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

        $table=Table::find($id);

        $data = $request->all();
        $data['user_id']=auth()->user()->id;
        $table->update($data);
        Toastr::success('Table Updated Successfully','Success');
        return redirect(route('tables.index'));
    }

   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Table::find($id)->delete();
        Toastr::success('Table Deleted Successfully','Success');
        return redirect(route('restaurants.index'));
    }

 
}
