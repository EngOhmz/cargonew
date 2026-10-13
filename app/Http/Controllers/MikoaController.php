<?php

namespace App\Http\Controllers;

use App\Models\Mikoa;
use Illuminate\Http\Request;

class MikoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $companyId = auth()->user()->company_id ?? null;
        $mikoa = $companyId
            ? Mikoa::where('company_id', $companyId)->orderBy('regional_name')->get()
            : collect();

        return view('mikoa.index', compact('mikoa'));
    }

    public function store(Request $request)
    {
        $request->validate(['regional_name' => 'required|string|max:255']);

        $companyId = auth()->user()->company_id;
        Mikoa::create([
            'company_id'   => $companyId,
            'regional_name' => $request->regional_name,
        ]);

        return redirect()->route('mikoa.index')->with('success', 'Mkoa imesajiliwa.');
    }

    public function destroy($id)
    {
        $companyId = auth()->user()->company_id;
        Mikoa::where('id', $id)->where('company_id', $companyId)->delete();
        return redirect()->route('mikoa.index')->with('success', 'Mkoa imefutwa.');
    }
}
