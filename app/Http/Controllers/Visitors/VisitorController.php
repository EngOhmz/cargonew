<?php

namespace App\Http\Controllers\Visitors;

use App\Http\Controllers\Controller;

use DB;
use App\Enums\Status;
use App\Enums\VisitorStatus;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;
use App\Models\Visitor;
use App\Models\Employee;
use App\Models\Cards\CardAssignment;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Visitors\VisitingDetails;
use App\Models\Cards\Cards;
use Yajra\DataTables\DataTables;
use App\Http\Requests\VisitorRequest;
use Illuminate\Support\Facades\Validator;
use App\Notifications\VisitorConfirmation;
use App\Http\Services\Visitor\VisitorService;
use Illuminate\Support\Facades\Hash;


class VisitorController extends Controller
{
    protected $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;

        // $this->middleware('auth');
        // $this->data['sitetitle'] = 'Visitors';

        // $this->middleware(['permission:visitors'])->only('index');
        // $this->middleware(['permission:visitors_create'])->only('create', 'store');
        // $this->middleware(['permission:visitors_edit'])->only('edit', 'update');
        // $this->middleware(['permission:visitors_delete'])->only('destroy');
        // $this->middleware(['permission:visitors_show'])->only('show');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.visitor.index');
    }


    public function create(Request $request)
    {

        //$this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        //$this->data
        return view('admin.visitor.create');
    }

    public function store(VisitorRequest $request)
    {
       $this->visitorService->make($request);
        return redirect()->route('visitors.index')->withSuccess('The data inserted successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $this->data['visitingDetails'] = $this->visitorService->find($id);
        if ($this->data['visitingDetails']) {
            return view('admin.visitor.show', $this->data);
        } else {
            return redirect()->route('admin.visitors.index');
        }
    }
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visitorID' => 'required|numeric',
        ], [
            'visitorID.required' => 'Visitor ID required',
            'visitorID.numeric' => 'ID must be numeric'
        ]);

        if ($validator->fails()) {
            return redirect(route('admin.visitors.index'))->withError($validator->errors()->first('visitorID'));
        };

        $id = $request->visitorID;

        $visitingDetail = VisitingDetails::where('reg_no', $id)->first();
        if ($visitingDetail && (!$visitingDetail->checkout_at)) {
            $visitingDetail->checkout_at = date('y-m-d H:i');
            $visitingDetail->save();
            return redirect()->route('admin.visitors.index')->withSuccess('Successfully Checked-Out!');
        } elseif (!$visitingDetail) {
            return redirect()->route('admin.visitors.index')->withError('ID not found');
        } else {

            return redirect()->route('admin.visitors.index')->withError('Already Checked-Out!');
        }
    }

    public function edit($id)
    {
        $this->data['employees'] = Employee::where('status', Status::ACTIVE)->get();
        $this->data['visitingDetails'] = $this->visitorService->find($id);
        if ($this->data['visitingDetails']) {
            return view('admin.visitor.edit', $this->data);
        } else {
            return redirect()->route('visitors.index');
        }
    }

    public function update(VisitorRequest $request, VisitingDetails $visitor)
    {
        $this->visitorService->update($request, $visitor->id);
        return redirect()->route('visitors.index')->withSuccess('The data updated successfully!');
    }

    public function destroy($id)
    {
        $this->visitorService->delete($id);
        return redirect()->route('visitors.index')->withSuccess('The data delete successfully!');
    }


    public function getVisitor(Request $request)
    {   
        if ($request->ajax()) {
        $data = VisitingDetails::all();
        return Datatables::of($data)
        ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $retAction = '';

                //if ((auth()->user()->can('visitors_show')) && (!$visitingDetail->checkout_at)) {
                    $retAction .= '<a href="' . route('visitors.checkout', $row->id) . '" class="btn btn-sm btn-icon mr-1  float-left btn-success" data-toggle="tooltip" data-placement="top" title="Check-Out"><i class="fas fa-sign-out-alt"></i></a>';
               // }

                //if (auth()->user()->can('visitors_show')) {
                    $retAction .= '<a href="' . route('visitors.show', $row->id) . '" class="btn btn-sm btn-icon mr-1  float-left btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="far fa-eye"></i></a>';
               // }

               // if (auth()->user()->can('visitors_edit')) {
                    $retAction .= '<a href="' . route('visitors.edit', $row->id) . '" class="btn btn-sm btn-icon mr-1 float-left btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"> <i class="far fa-edit"></i></a>';
               // }


               // if (auth()->user()->can('visitors_delete')) {
                    $retAction .= '<form class="float-left  " action="' . route('visitors.destroy', $row->id) . '" method="POST">' . method_field('DELETE') . csrf_field() . '<button class="btn btn-sm btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="fa fa-trash"></i></button></form>';
               // }

                return $retAction;
            })

            ->editColumn('name', function ($row) {
                return Str::limit(optional($row->visitor)->name, 50);
            })
            // ->editColumn('visitor_id', function ($visitingDetail) {
            //     return $visitingDetail->reg_no;
            // })
            ->editColumn('status', function ($row) {
                $drop = '';
                $dropActive = $row->status;
                $activeStatus = 'Assign Card';


                if ($dropActive == 0) {
                    return '<div class="dropdown">
                    <a href="' . route('visitors.assignCard', $row->visitor_id) . '" class="btn btn-sm btn-icon mr-1  float-left btn-secondary" data-toggle="tooltip" data-placement="top" title="View">'
                        . $activeStatus
                        . '</a>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' . $drop . '</div></div>';
                } elseif($dropActive == 1) {
                $card  =  Cards::find($row->card_id);
                if(!empty($card))
                    $card_no = $card->reference_no;
                    else
                    $card_no =0;
                    return '<span class="badge ' . ($row->status == 1 ? 'badge-success' : 'badge-danger') . '">Assigned</span> card no is::'.$card_no.' ';
                }
            })
            ->editColumn('date', function ($row) {
                if ($row->checkin_at) {
                    return date('d-m-Y h:i A', strtotime($row->checkin_at));
                } else {
                    return 'N/A';
                }
            })
            ->editColumn('checkout', function ($row) {
                if ($row->checkout_at) {
                    return date('d-m-Y h:i A', strtotime($row->checkout_at));
                } else {
                    return 'N/A';
                }
            })

            // ->editColumn('id', function ($visitingDetail) {
            //     return $visitingDetail->setID;
            // })
            ->rawColumns(['action','status'])
            //->escapeColumns([])
            ->make(true);
        }
    }
    public function checkout(VisitingDetails $visitingDetail)
    {

        $visitingDetail->checkout_at = date('y-m-d H:i');
        $visitingDetail->save();
        return redirect()->route('visitors.index')->withSuccess('Successfully Check-Out!');
    }

    public function changeStatus($id, $status)
    {
        $visitor         = VisitingDetails::findOrFail($id);
        $visitor->status = $status;
        $visitor->checkin_at = date('y-m-d H:i');
        $visitor->save();
        try {
            $visitor->visitor->notify(new VisitorConfirmation($visitor));
            return redirect()->route('visitors.index')->withSuccess('The Status Change successfully!');
        } catch (\Exception $e) {
        }
    }


    public function assignCard($id){

        $card = Cards::where('status',1)->get()->first();
        if(!empty($card))
        $card_id = $card->id;
        $visitor_id = $id;

        if(isset($card_id)){
            $data['visitor_id'] = $visitor_id;
            $data['cards_id'] = $card_id;
            $data['added_by'] = auth()->user()->id;

            $assignment  = CardAssignment::create($data);
         }else{

            return redirect()->back()->with(['error'=>'No Card available']);
         }
        if(!empty($assignment->id) && $assignment->id > 0){
            Cards::where('id',$card_id)->update(['status'=>2,'owner_id'=>$visitor_id]);
            VisitingDetails::where('visitor_id',$visitor_id)->update(['status'=>1,'card_id'=>$card_id]);

        }

      
        return redirect()->back()->with(['success'=>'Card assigned successfull']);


    }
}