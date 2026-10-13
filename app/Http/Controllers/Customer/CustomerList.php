<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerPacel;
use App\Support\ParcelDropAnalysis;
use DataTables;

class CustomerList extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query()
                ->select([
                    'customers.id',
                    'customers.mteja',
                    'customers.customer_phone',
                    'customers.mpokeaji',
                    'customers.receiver_phone',
                    'customers.added_by',
                    'customers.created_at',
                    'users.name as registered_by_name',
                ])
                ->leftJoin('users', 'users.id', '=', 'customers.added_by')
                ->orderBy('customers.created_at', 'desc');

            $companyId = auth()->user()->company_id ?? null;
            if (!is_null($companyId)) {
                $query->where('customers.company_id', $companyId);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('customers.created_at', '>=', $request->start_date)
                      ->whereDate('customers.created_at', '<=', $request->end_date);
            }

            if ($request->filled('customer_id')) {
                $query->where('customers.id', $request->input('customer_id'));
            }

            return Datatables::of($query)
                ->editColumn('id', function ($row) {
                    $refNo = 'CSTM-' . str_pad($row->id, 3, '0', STR_PAD_LEFT);
                    return '<a href="' . route('list_ya_wateja.detail', $row->id) . '" class="ref-link">' . $refNo . '</a>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('registered_by', function ($row) {
                    return $row->registered_by_name ?: 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return view('customer.action', compact('row'));
                })
                ->rawColumns(['action', 'id'])
                ->make(true);
        }

        return view('customer.customer_list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id, Request $request)
    {
        $data2 = Customer::findOrFail($id);
        $agent = \App\Models\User::find($data2->added_by);

        $company = null;
        if (auth()->check() && auth()->user()->company_id) {
            $company = \App\Models\Company::find(auth()->user()->company_id);
        }

        $data = CustomerPacel::with('car')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $parcelIds = $data->pluck('id')->all();
        $dropAnalysisByParcel = ParcelDropAnalysis::forParcels($parcelIds);

        return view('customer.customer_show', compact('data2', 'data', 'dropAnalysisByParcel', 'company', 'agent'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
