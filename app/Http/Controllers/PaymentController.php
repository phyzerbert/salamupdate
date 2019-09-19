<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Company;

use Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $type, $id)
    {
        if($type == 'purchase'){
            config(['site.page' => 'purchase']);
            $paymentable = Purchase::find($id);
        }else if($type == 'sale'){
            config(['site.page' => 'sale']);
            $paymentable = Sale::find($id);
        }
        $data = $paymentable->payments;
        return view('payment.index', compact('data', 'type', 'id'));
    }

    public function create(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_no'=>'required|string',
            'type'=>'required|string',
            'paymentable_id'=>'required',
        ]);

        $user = Auth::user();
        
        $item = new Payment();
        $item->timestamp = $request->get('date').":00";
        $item->reference_no = $request->get('reference_no');
        $item->amount = $request->get('amount');
        $item->paymentable_id = $request->get('paymentable_id');
        $item->note = $request->get('note');
        if($request->get('type') == 'purchase'){
            $item->paymentable_type = Purchase::class;
        }else if($request->get('type') == 'sale'){
            $item->paymentable_type = Sale::class;
        }
        if(Auth::user()->hasRole('user')){
            $item->status = 1;
        }else{
            $item->status = 0;
        }
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "payment_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/payment_images/'), $imageName);
            $item->attachment = 'images/uploaded/payment_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.added_successfully'));
    }

    public function edit(Request $request){
        $request->validate([
            'date'=>'required',
        ]);
        $data = $request->all();
        $item = Payment::find($request->get("id"));
        $item->timestamp = $request->get("date");
        $item->reference_no = $request->get("reference_no");
        $item->amount = $request->get("amount");
        $item->note = $request->get("note");
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "payment_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/payment_images/'), $imageName);
            $item->attachment = 'images/uploaded/payment_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', __('page.updated_successfully'));
    }


    public function delete($id){
        $item = Payment::find($id);
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    public function approve($id){
        $item = Payment::find($id);
        $item->update(['status' => 1]);
        return back()->with("success", __('page.approved_successfully'));
    }

    public function pending_payments(Request $request){
        config(['site.page' => 'pending_payments']);
        $companies = Company::all();
        $user = Auth::user();
        $mod = new Payment();
        $mod = $mod->where('status', 0);

        $company_id = $reference_no = $period = '';
        if($user->hasRole('user')){
            $company_id = $user->company_id;            
        }
        if($request->get('company_id') != ''){
            $company_id = $request->get('company_id');
        }
        if($company_id != ''){
            $company = Company::find($company_id);
            $company_purchases = $company->purchases()->pluck('id');
            $company_sales = $company->sales()->pluck('id');
            $mod = $mod->where(function($query) use($company_purchases, $company_sales){
                $query->whereIn('paymentable_id', $company_purchases)->where('paymentable_type', Purchase::class)
                    ->orWhereIn('paymentable_id', $company_sales)->where('paymentable_type', Sale::class);
            });
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('payment.pending', compact('data', 'companies', 'company_id', 'reference_no', 'period'));
    }
}
