<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;

use App\Traits\Calculate_netProfitTrait;
use App\Traits\Calculate_netProfitTrait2;
use App\Traits\Calculate_netProfitTrait3;
use App\Traits\Calculate_netProfitTrait4;
use App\Models\Accounting\ClassAccount;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\ButtonsServiceProvider;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Models\Pacel;
use App\Region;
use App\User;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $region = Region::all();
        if ($request->ajax()) {
            $data = Pacel::query();
            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
            $status = (!empty($_GET["status"])) ? ($_GET["status"]) : ('');
            $from = (!empty($_GET["from"])) ? ($_GET["from"]) : ('');
            $to = (!empty($_GET["to"])) ? ($_GET["to"]) : ('');

     //filter selected columns
            if($start_date && $end_date){
             $start_date = date('Y-m-d', strtotime($start_date));
             $end_date = date('Y-m-d', strtotime($end_date));
             $data->whereRaw("date(pacels.created_at) >= '" . $start_date . "' AND date(pacels.created_at) <= '" . $end_date . "'");
            }
            if($from && $from!="all")
            $data->whereRaw("pacels.from = '" . $from . "'");
            if($to && $to!="all")
            $data->whereRaw("pacels.to = '" . $to . "'");
            if($status && $status!="all")
            $data->whereRaw("pacels.status = '" . $status . "'");
            $data2 = $data->select('*');


            return Datatables::of($data2)
                    ->addIndexColumn()
                    ->editColumn('date', function ($row) {
                        $newDate = date("d-m-Y", strtotime($row->created_at));
                        return '- '.$newDate.'<br>- Ref.no '.$row->pacel_number;
                   })
                   ->editColumn('pacel_number', function ($row) {
                    $user = User::find($row->owner_id);
                    return '- '.$user->fname.' '.$user->lname.'<Br>- '.$user->address.'<br>- '.$user->country;
               })
               ->editColumn('from', function ($row) {
                
                return $row->receiver_name;
           })
                    ->editColumn('weight', function ($row) {
                         return $row->weight.'kg';
                    })
                    ->editColumn('from_to', function ($row) {
                        return '- '.$row->from.'<br>- '.$row->to.'<br>- '.$row->pacel_name;
                   })
                    ->editColumn('amount', function ($row) {
                        return $row->amount.' /=Tsh';
                   })
                    ->editColumn('status', function ($row) {
                        if($row->status == 1)
                         return '<span class="label label-warning">payment processed</span>';
                        elseif($row->status == 2)
                        return '<span class="label label-info">payment confirmed</span>';
                        elseif($row->status == 3)
                        return '<span class="label label-success">Collected</span>';
                        elseif($row->status == 4)
                        return '<span class="label label-info">On Transit</span>';
                        elseif($row->status == 5)
                        return '<span class="label label-primary">arrive</span>';
                        elseif($row->status == 6)
                        return '<span class="label label-primary">derivered</span>';
                        
                    })
                    ->rawColumns(['status','date','pacel_number','from_to'])
                   
                    
                    ->make(true);
        }
      
        return view('report.pacel',compact('region'));
    

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


  use Calculate_netProfitTrait3;
     use Calculate_netProfitTrait4;
    public function trial_balance(Request $request)
    {
       
        $start_date = $request->start_date;
         $second_date = $request->second_date;
        //$end_date = $request->end_date;

         $income = ClassAccount::where('class_type','Income')->get();
           $cost = ClassAccount::where('class_type','Expense')->get();
           $expense= ClassAccount::where('class_type','Expense')->get();


       $data = ClassAccount::all();
        return view('financial_report.trial_balance',
            compact('start_date','second_date','income','expense',
                'cost' ,'data'));
    }
    use Calculate_netProfitTrait3;
    use Calculate_netProfitTrait4;
   public function trial_balance_summary(Request $request)
   {
      
       $start_date = $request->start_date;
        $second_date = $request->second_date;
       //$end_date = $request->end_date;

        $income = ClassAccount::where('class_type','Income')->get();
          $cost = ClassAccount::where('class_type','Expense')->get();
          $expense= ClassAccount::where('class_type','Expense')->get();


      $data = ClassAccount::all();
       return view('financial_report.trial_balance_summary',
           compact('start_date','second_date','income','expense',
               'cost' ,'data'));
   }
    public function trial_balance_pdf(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
     $data = ClassAccount::all();
        $pdf = PDF::loadView('financial_report.trial_balance_pdf', compact('start_date',
            'end_date','data'));
        return $pdf->download(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date . ".pdf");

    }

    public function trial_balance_excel(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.trial_balance',
                    1) . " " . trans_choice('general.for', 1) . " " . trans_choice('general.period',
                    1) . ":" . $start_date . " " . trans_choice('general.to', 1) . " " . $end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1)
            ]);
            $credit_total = 0;
            $debit_total = 0;
            foreach (ChartOfAccount::orderBy('gl_code', 'asc')->get() as $key) {
                $cr = 0;
                $dr = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $credit_total = $credit_total + $cr;
                $debit_total = $debit_total + $dr;
                array_push($data, [$key->gl_code, $key->name, number_format($dr, 2), number_format($cr, 2)]);
            }
            array_push($data, [
                trans_choice('general.total', 1),
                "",
                number_format($debit_total, 2),
                number_format($credit_total, 2)
            ]);

            Excel::create(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:D1');
                    });

                })->download('xls');
        }
    }

    public function trial_balance_csv(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.trial_balance',
                    1) . " " . trans_choice('general.for', 1) . " " . trans_choice('general.period',
                    1) . ":" . $start_date . " " . trans_choice('general.to', 1) . " " . $end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.debit', 1),
                trans_choice('general.credit', 1)
            ]);
            $credit_total = 0;
            $debit_total = 0;
            foreach (ChartOfAccount::orderBy('gl_code', 'asc')->get() as $key) {
                $cr = 0;
                $dr = 0;
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $credit_total = $credit_total + $cr;
                $debit_total = $debit_total + $dr;
                array_push($data, [$key->gl_code, $key->name, number_format($dr, 2), number_format($cr, 2)]);
            }
            array_push($data, [
                trans_choice('general.total', 1),
                "",
                number_format($debit_total, 2),
                number_format($credit_total, 2)
            ]);

            Excel::create(trans_choice('general.trial_balance', 1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:D1');
                    });

                })->download('csv');
        }
    }

    public function income_statement(Request $request)
    {
       
        $start_date = $request->start_date;
         $second_date = $request->second_date;
        $end_date = $request->end_date;
        
        
           $income = ClassAccount::where('class_type','Income')->get();
           $cost = ClassAccount::where('class_type','Expense')->get();
           $expense= ClassAccount::where('class_type','Expense')->get();

        return view('financial_report.income_statement',
            compact('start_date','second_date','income','expense','end_date',
                'cost'));
    }
    public function income_statement_summary(Request $request)
    {
       
        $start_date = $request->start_date;
         $second_date = $request->second_date;
        $end_date = $request->end_date;
        
        
           $income = ClassAccount::where('class_type','Income')->get();
           $cost = ClassAccount::where('class_type','Expense')->get();
           $expense= ClassAccount::where('class_type','Expense')->get();

        return view('financial_report.income_statement_summary',
            compact('start_date','second_date','income','expense','end_date',
                'cost'));
    }


    public function income_statement_pdf(Request $request)
    {
       
        $start_date = $request->start_date;
           $second_date = $request->second_date;
        $end_date = $request->end_date;
   $sales = ClassAccount::where('class_type','Sales')->get();
    $cost = ClassAccount::where('class_type','Cost of Goods Sold')->get();
   $expense= ClassAccount::where('class_type','Expense')->get();
        $pdf = PDF::loadView('financial_report.income_statement_pdf', compact('start_date','second_date','sales','expense','end_date',
                'cost'));
        return $pdf->download(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                1) . ' : ' . $request->second_date . ".pdf");
    }

    public function income_statement_excel(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                "",
                trans_choice('general.income', 1),
                ""
            ]);
            $total_income = 0;
            $total_expenses = 0;
            foreach (ChartOfAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.income', 1),
                number_format($total_income, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.expense', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.expense', 2),
                number_format($total_expenses, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.net', 1) . " " . trans_choice('general.income', 2),
                number_format($total_income - $total_expenses, 2)
            ]);

            Excel::create(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('xls');
        }
    }

    public function income_statement_csv(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                "",
                trans_choice('general.income', 1),
                ""
            ]);
            $total_income = 0;
            $total_expenses = 0;
            foreach (ChartOfAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_income = $total_income + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.income', 1),
                number_format($total_income, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.expense', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->whereBetween('date',
                    [$start_date, $end_date])->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_expenses = $total_expenses + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.expense', 2),
                number_format($total_expenses, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.net', 1) . " " . trans_choice('general.income', 2),
                number_format($total_income - $total_expenses, 2)
            ]);

            Excel::create(trans_choice('general.income', 1) . ' ' . trans_choice('general.statement',
                    1) . ' : ' . $request->end_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('csv');
        }
    }
    
    
    use Calculate_netProfitTrait;
     use Calculate_netProfitTrait2;
    public function balance_sheet(Request $request)
    {  
       
         $start_date = $request->start_date;
   $end_date = $request->end_date;
        $asset = ClassAccount::where('class_type','Assets')->get();
    $liability = ClassAccount::where('class_type','Liability')->get();
   $equity = ClassAccount::where('class_type','Equity')->get();

  $income = ClassAccount::where('class_type','Income')->get();
           $cost = ClassAccount::where('class_type','Expense')->get();
           $expense= ClassAccount::where('class_type','Expense')->get();

  if(!empty($start_date)){
          $net_profit = $this->get_netProfit($start_date,$end_date);
        }
else{
     $net_profit ='';      
}

$net_p = $this->get_netProfit2();
       return view('financial_report.balance_sheet',
            compact('start_date','income','expense',
                'cost' ,'end_date','asset','liability',
                'equity','net_p','net_profit'));
    }
    
    use Calculate_netProfitTrait;
    use Calculate_netProfitTrait2;
   public function balance_sheet_summary(Request $request)
   {  
      
        $start_date = $request->start_date;
  $end_date = $request->end_date;
       $asset = ClassAccount::where('class_type','Assets')->get();
   $liability = ClassAccount::where('class_type','Liability')->get();
  $equity = ClassAccount::where('class_type','Equity')->get();

 $income = ClassAccount::where('class_type','Income')->get();
          $cost = ClassAccount::where('class_type','Expense')->get();
          $expense= ClassAccount::where('class_type','Expense')->get();

 if(!empty($start_date)){
         $net_profit = $this->get_netProfit($start_date,$end_date);
       }
else{
    $net_profit ='';      
}

$net_p = $this->get_netProfit2();
      return view('financial_report.balance_sheet_summary',
           compact('start_date','income','expense',
               'cost' ,'end_date','asset','liability',
               'equity','net_p','net_profit'));
   }

    public function balance_sheet1(Request $request)
    {
       
        $start_date = $request->start_date;
   $end_date = $request->end_date;
        $asset = ClassAccount::where('class_type','Assets')->get();
    $liability = ClassAccount::where('class_type','Liability')->get();
   $equity = ClassAccount::where('class_type','Equity')->get();
        return view('financial_report.balance_sheet',
            compact('start_date','end_date','asset','liability',
                'equity'));
    }

    public function balance_sheet_pdf(Request $request)
    {
       
          $start_date = $request->start_date;
       $asset = ClassAccount::where('class_type','Assets')->get();
    $liability = ClassAccount::where('class_type','Liability')->get();
   $equity = ClassAccount::where('class_type','Equity')->get();

        $pdf = PDF::loadView('financial_report.balance_sheet_pdf', compact('start_date',
             'asset','liability',
                'equity'));
        return $pdf->download(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                1) . ' : ' . $request->start_date . ".pdf");
    }

    public function balance_sheet_excel(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                trans_choice('general.asset', 2),
                "",
                ""
            ]);
            $total_liabilities = 0;
            $total_assets = 0;
            $total_equity = 0;
            $retained_earnings = 0;
            foreach (ChartOfAccount::where('account_type', 'asset')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.asset', 2),
                number_format($total_assets, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.liability', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'liability')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability', 2),
                number_format($total_liabilities, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.equity', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'equity')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.equity', 2),
                number_format($total_equity, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability',
                    2) . " " . trans_choice('general.and', 2) . " " . trans_choice('general.equity', 2),
                number_format($total_liabilities + $total_equity, 2)
            ]);


            Excel::create(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('xls');
        }
    }

    public function balance_sheet_csv(Request $request)
    {
       
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (!empty($start_date)) {
            $data = [];
            array_push($data, [
                trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date
            ]);
            array_push($data, [
                trans_choice('general.gl_code', 1),
                trans_choice('general.account', 1),
                trans_choice('general.balance', 1),
            ]);
            array_push($data, [
                trans_choice('general.asset', 2),
                "",
                ""
            ]);
            $total_liabilities = 0;
            $total_assets = 0;
            $total_equity = 0;
            $retained_earnings = 0;
            foreach (ChartOfAccount::where('account_type', 'asset')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.asset', 2),
                number_format($total_assets, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.liability', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'liability')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability', 2),
                number_format($total_liabilities, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.equity', 2),
                ""
            ]);
            foreach (ChartOfAccount::where('account_type', 'equity')->orderBy('gl_code', 'asc')->get() as $key) {
                $cr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('credit');
                $dr = \App\Models\JournalEntry::where('account_id', $key->id)->where('date', '<=',
                    $start_date)->where('branch_id',
                    session('branch_id'))->sum('debit');
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
                array_push($data, [$key->gl_code, $key->name, number_format($balance, 2)]);
            }
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.equity', 2),
                number_format($total_equity, 2)
            ]);
            array_push($data, [
                "",
                trans_choice('general.total', 1) . " " . trans_choice('general.liability',
                    2) . " " . trans_choice('general.and', 2) . " " . trans_choice('general.equity', 2),
                number_format($total_liabilities + $total_equity, 2)
            ]);


            Excel::create(trans_choice('general.balance', 1) . ' ' . trans_choice('general.sheet',
                    1) . ' : ' . $request->start_date,
                function ($excel) use ($data) {
                    $excel->sheet('Sheet', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', false, false);
                        $sheet->mergeCells('A1:C1');
                    });

                })->download('csv');
        }
    }



}
