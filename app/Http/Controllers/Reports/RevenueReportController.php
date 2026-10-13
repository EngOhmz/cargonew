<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\CustomerPacel;
use App\Models\DocumentSetting;
use DataTables;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RevenueReportExport;

class RevenueReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CustomerPacel::where('company_id', auth()->user()->company_id);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereDate('created_at', '>=', $request->start_date)
                      ->whereDate('created_at', '<=', $request->end_date);
            }

            $query->orderBy('created_at', 'desc');

            return Datatables::of($query)
                ->addIndexColumn()
                ->addColumn('route', function ($row) {
                    return $row->mzigo_unapotoka . ' - ' . $row->mzigo_unapokwenda;
                })
                ->addColumn('balance', function ($row) {
                    return (float)$row->jumla - (float)$row->ela_iliyopokelewa;
                })
                ->editColumn('bei', function ($row) {
                    return (float)$row->bei;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }

        return view('reports.revenue_report');
    }

    public function exportPdf(Request $request)
    {
        $query = CustomerPacel::where('company_id', auth()->user()->company_id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $totals = [
            'jumla' => $data->sum('jumla'),
            'received' => $data->sum('ela_iliyopokelewa'),
            'balance' => $data->sum(function($row) {
                return (float)$row->jumla - (float)$row->ela_iliyopokelewa;
            })
        ];

        $settings = $this->getDocumentSettingsVars();
        $startDate = $request->start_date ?: 'All Records';
        $endDate = $request->end_date ?: '';

        $pdf = PDF::loadView('reports.revenue_report_pdf', array_merge(compact('data', 'totals', 'startDate', 'endDate'), $settings));
        $filename = ($request->filled('start_date') && $request->filled('end_date'))
            ? 'revenue_report_' . $request->start_date . '_to_' . $request->end_date . '.pdf'
            : 'revenue_report_all.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $filename = ($startDate && $endDate)
            ? 'revenue_report_' . $startDate . '_to_' . $endDate . '.xlsx'
            : 'revenue_report_all.xlsx';

        return Excel::download(new RevenueReportExport($startDate, $endDate), $filename);
    }

    private function getDocumentSettingsVars()
    {
        $settings = DocumentSetting::where('company_id', auth()->user()->company_id)->first();
        return [
            'branch' => $settings->branch ?? '',
            'contact' => $settings->contact ?? '',
            'tunasafirisha' => $settings->tunasafirisha ?? '',
        ];
    }
}
