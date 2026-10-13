<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;

use App\Models\Accounting\ClassAccount;
use App\Models\Accounting\AccountType;
use App\Models\Accounting\ChartOfAccount;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use DB;

class ChartOfAccountController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $data = AccountType::all();

   $class_account = DB::table('gl_account_class')->select('gl_account_class.class_type')->groupBy('class_type')->get();
        return view('accounting.chart_of_account.data', compact('data','class_account'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return view('accounting.chart_of_account.create', compact(''));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Sentinel::hasAccess('capital.create')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $rules = array(
            'name' => 'required',
            'gl_code' => 'required|unique:chart_of_accounts',
            'account_type' => 'required'
        );
        $messages = [
            'name.required' => 'Name is required',
            'gl_code.required' => 'GL Code is required',
            'mobile_phone.required' => 'Mobile number is required',
            'gl_code.unique' => 'The GL Code already exists',
            'account_type.required' => 'Account type is required',
        ];
        $validator = Validator::make(Input::all(), $rules, $messages);
        if ($validator->fails()) {
            Flash::warning(trans('general.validation_error'));
            return redirect()->back()->withInput()->withErrors($validator);

        } else {
            $chart_of_account = new ChartOfAccount();
            $chart_of_account->name = $request->name;
            $chart_of_account->parent_id = $request->parent_id;
            $chart_of_account->gl_code = $request->gl_code;
            $chart_of_account->account_type = $request->account_type;
            $chart_of_account->notes = $request->notes;
            $chart_of_account->save();
            Flash::success(trans('general.successfully_saved'));
            return redirect('chart_of_account/data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($chart_of_account)
    {
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        return View::make('chart_of_account.edit', compact('chart_of_account'))->render();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Sentinel::hasAccess('capital.update')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        $chart_of_account = ChartOfAccount::find($id);
        $chart_of_account->name = $request->name;
        $chart_of_account->parent_id = $request->parent_id;
        $chart_of_account->gl_code = $request->gl_code;
        $chart_of_account->account_type = $request->account_type;
        $chart_of_account->notes = $request->notes;
        $chart_of_account->save();
        Flash::success(trans('general.successfully_saved'));
        return redirect('chart_of_account/data');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        if (!Sentinel::hasAccess('capital.delete')) {
            Flash::warning("Permission Denied");
            return redirect('/');
        }
        ChartOfAccount::destroy($id);
        Flash::success(trans('general.successfully_deleted'));
        return redirect('chart_of_account/data');
    }
}
