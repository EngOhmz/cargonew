<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerPacel;
use App\Models\ParcelCarAssignment;
use App\Support\ParcelDropAnalysis;
use App\Support\ParcelStatusLabel;
use DataTables;

class PacelList extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CustomerPacel::with('car')
                ->select('*')
                ->where('company_id', auth()->user()->company_id); // Filter by company_id

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('created_at', '>=', $request->start_date)
                      ->whereDate('created_at', '<=', $request->end_date);
            }

            $query->orderBy('created_at', 'desc');

            return Datatables::of($query)
                ->addIndexColumn()
                ->editColumn('hashtag', function ($row) {
                    $refNo = $row->hashtag ?: $row->id;
                    return '<a href="' . route('list_ya_mizigo.show', $row->id) . '" style="color: blue; text-decoration: underline; font-weight: bold;">RG-' . $refNo . '</a>';
                })
                ->editColumn('created_at', function ($row) {
                  return $row->created_at->format('Y-m-d H:i:s');
                })
                ->editColumn('activity', function ($row) {
                    $label = ParcelStatusLabel::label(
                        $row->activity,
                        optional($row->car)->status,
                        $row->idadi_shusha,
                        $row->idadi
                    );
                    $classes = ParcelStatusLabel::badgeClasses(
                        $row->activity,
                        optional($row->car)->status
                    );

                    return '<span class="status-badge ' . $classes . '">' . e($label) . '</span>';
                })
                ->rawColumns(['activity', 'hashtag'])
                ->make(true);
        }

        return view('customer.pacel_list');
    }

    public function show($id)
    {
        $parcel = CustomerPacel::with(['customer', 'car'])->findOrFail($id);

        $packingAssignments = ParcelCarAssignment::with(['car:id,carNumber', 'parcel:id,name'])
            ->where('pacel_id', $parcel->id)
            ->get();

        $hasBeenPackedOrShipped = (int) ($parcel->idadi_kupakia ?? 0) > 0
            || (int) ($parcel->idadi_shusha ?? 0) > 0
            || !empty($parcel->car_id)
            || $packingAssignments->isNotEmpty();

        $dropInfo = null;
        $dropDetailsNjiani = collect();
        $dropDetailsMwisho = collect();
        $dropSummary = [
            'njiani_qty' => 0,
            'mwisho_qty' => 0,
            'njiani_amount' => 0.0,
            'mwisho_amount' => 0.0,
        ];

        if ((int) ($parcel->idadi_shusha ?? 0) > 0) {
            $dropInfo = \App\Models\PacelHistory::where('pacel_id', $parcel->id)
                ->where('activity', 'kushusha')
                ->orderBy('id', 'desc')
                ->first();

            $analysis = ParcelDropAnalysis::forParcel(
                (int) $parcel->id,
                $parcel->mzigo_unapokwenda
            );
            $dropDetailsNjiani = $analysis['njiani'];
            $dropDetailsMwisho = $analysis['mwisho'];
            $dropSummary = $analysis['summary'];
        }

        return view('customer.pacel_show', compact(
            'parcel',
            'packingAssignments',
            'dropInfo',
            'dropDetailsNjiani',
            'dropDetailsMwisho',
            'dropSummary',
            'hasBeenPackedOrShipped'
        ));
    }
}



