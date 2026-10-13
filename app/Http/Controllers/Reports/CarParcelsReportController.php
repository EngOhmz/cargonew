<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DocumentSetting;
use App\Models\Management\Car;
use App\Models\PacelHistory;
use Illuminate\Http\Request;
use DataTables;
use PDF;

class CarParcelsReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->baseQuery($request);

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('car_number', function ($row) {
                    return optional($row->car)->carNumber ?: '—';
                })
                ->addColumn('delivery', function ($row) {
                    return $row->delivery ?: '—';
                })
                ->addColumn('route', function ($row) {
                    return trim(($row->mzigo_unapotoka ?: '—') . ' → ' . ($row->mzigo_unapokwenda ?: '—'));
                })
                ->addColumn('balance', function ($row) {
                    return (float) $row->jumla - (float) $row->ela_iliyopokelewa;
                })
                ->editColumn('idadi_kupakia', function ($row) {
                    return $row->idadi_kupakia ?: $row->idadi;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i') : '';
                })
                ->rawColumns([])
                ->make(true);
        }

        $cars = Car::where('company_id', auth()->user()->company_id)
            ->orderBy('carNumber')
            ->get(['id', 'carNumber']);

        return view('reports.car_parcels_report', compact('cars'));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->baseQuery($request)
            ->with('car')
            ->orderBy('created_at', 'desc')
            ->get();

        $totals = [
            'qty' => $data->sum(function ($row) {
                return (float) ($row->idadi_kupakia ?: $row->idadi);
            }),
            'jumla' => $data->sum('jumla'),
            'received' => $data->sum('ela_iliyopokelewa'),
            'balance' => $data->sum(function ($row) {
                return (float) $row->jumla - (float) $row->ela_iliyopokelewa;
            }),
        ];

        $carLabel = 'Gari zote';
        if ($request->filled('car_id')) {
            $car = Car::find($request->car_id);
            $carLabel = $car ? $car->carNumber : ('#' . $request->car_id);
        }

        $settings = $this->getDocumentSettingsVars();
        $startDate = $request->start_date ?: 'All';
        $endDate = $request->end_date ?: '';

        $pdf = PDF::loadView(
            'reports.car_parcels_report_pdf',
            array_merge(compact('data', 'totals', 'startDate', 'endDate', 'carLabel'), $settings)
        )->setPaper('a4', 'landscape');

        $filename = 'car_parcels_report_' . now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    protected function baseQuery(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = PacelHistory::query()
            ->with('car')
            ->where('company_id', $companyId)
            ->whereNotNull('car_id')
            ->where('car_id', '!=', 0)
            ->where(function ($q) {
                $q->where('idadi_kupakia', '!=', '0')
                    ->orWhere('activity', 'kupakia')
                    ->orWhere('activity', 'ipo njiani')
                    ->orWhere('activity', 'kushusha');
            });

        if ($request->filled('car_id')) {
            $query->where('car_id', (int) $request->car_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query->orderByDesc('created_at');
    }

    private function getDocumentSettingsVars()
    {
        $settings = DocumentSetting::where('company_id', auth()->user()->company_id)->first();

        return [
            'branch' => $settings->branch ?? ($settings->office_1_place ?? ''),
            'contact' => $settings->contact ?? ($settings->office_1_contact ?? ''),
            'tunasafirisha' => $settings->tunasafirisha ?? ($settings->header_line1 ?? ''),
        ];
    }
}
