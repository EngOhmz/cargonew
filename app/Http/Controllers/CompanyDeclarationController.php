<?php

namespace App\Http\Controllers;

use App\Models\CompanyDeclaration;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;

class CompanyDeclarationController extends Controller
{
    public function schedule(Schedule $schedule){
        $schedule->call(function () {
            DB::table('cars')->whereNotNull('closeDate')->whereRaw('closeDate < now()')->update(['status' => '1']);
        })->everyMinute();
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompanyDeclaration  $companyDeclaration
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyDeclaration $companyDeclaration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CompanyDeclaration  $companyDeclaration
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyDeclaration $companyDeclaration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompanyDeclaration  $companyDeclaration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyDeclaration $companyDeclaration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompanyDeclaration  $companyDeclaration
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyDeclaration $companyDeclaration)
    {
        //
    }
}
