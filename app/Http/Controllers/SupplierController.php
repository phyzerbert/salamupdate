<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Company;
use App\Models\Image;

use App\Mail\ReportMail;
use App\Exports\SupplierExport;

use Auth;
use PDF;
use Mail;
use Excel;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        config(['site.page' => 'supplier']);
        $mod = new Supplier();
        $company = $name = $phone_number = '';
        if ($request->get('company') != ""){
            $company = $request->get('company');
            $mod = $mod->where('company', 'LIKE', "%$company%");
        }
        if ($request->get('name') != ""){
            $name = $request->get('name');
            $mod = $mod->where('name', 'LIKE', "%$name%");
        }
        if ($request->get('phone_number') != ""){
            $phone_number = $request->get('phone_number');
            $mod = $mod->where('phone_number', 'LIKE', "%$phone_number%");
        }
        $pagesize = session('pagesize');
        if(!$pagesize){$pagesize = 15;}
        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('admin.suppliers', compact('data', 'name', 'company', 'phone_number'));
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        Supplier::create([
            'name' => $request->get('name'),
            'company' => $request->get('company'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'note' => $request->get('note'),
        ]);
        return response()->json('success');
    }

    public function purchase_create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        $supplier = Supplier::create([
            'name' => $request->get('name'),
            'company' => $request->get('company'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'note' => $request->get('note'),
        ]);

        return response()->json($supplier);
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $item = Supplier::find($request->get("id"));
        $item->name = $request->get("name");
        $item->company = $request->get("company");
        $item->email = $request->get("email");
        $item->phone_number = $request->get("phone_number");
        $item->address = $request->get("address");
        $item->city = $request->get("city");
        $item->note = $request->get("note");
        $item->save();
        return response()->json('success');
    }

    public function delete($id){
        $item = Supplier::find($id);
        if(!$item){
            return back()->withErrors(["delete" => __('page.something_went_wrong')]);
        }
        $purchases = $item->purchases()->where('status', 0)->get();
        if($purchases->isNotEmpty()){
            return back()->withErrors(['pending' => __('page.supplier_cant_delete')]);
        }
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    
    public function report($id){
        $supplier = Supplier::find($id);
        $pdf = PDF::loadView('reports.suppliers_report.pdf', compact('supplier'))->setPaper('a4', 'landscape');        
        return $pdf->download('supplier_report_'.$supplier->company.'.pdf');        
        // return view('reports.suppliers_report.pdf', compact('supplier'));
    }
    
    public function email($id){
        $supplier = Supplier::find($id);
        $pdf = PDF::loadView('reports.suppliers_report.pdf', compact('supplier'))->setPaper('a4', 'landscape');   
        if(filter_var($supplier->email, FILTER_VALIDATE_EMAIL)){
            $to_email = $supplier->email;
            Mail::to($to_email)->send(new ReportMail($pdf, 'Supplier Report'));
            return back()->with('success', __('page.email_is_sent_successfully'));
        }else{
            return back()->withErrors('email', __('page.invalid_email_address'));
        }
    }

    public function export($id) { 
        $user = Auth::user();
        $supplier = Supplier::find($id);
        if($user->company) {
            $company_id = $user->company_id;
            $purchases = $supplier->purchases()->where('company_id', $company_id)->get();
            $purchase_array = $supplier->purchases()->where('company_id', $company_id)->pluck('id');
        }
        $purchases = $supplier->purchases;
        $purchase_array = $supplier->purchases->pluck('id');
        $payments = Payment::whereIn('paymentable_id', $purchase_array)->where('paymentable_type', 'App\Models\Purchase')->get();
        return Excel::download(new SupplierExport($purchases, $payments), 'supplier_report.xlsx');
    }

    public function concurrent_payments(Request $request){
        config(['site.page' => 'concurrent_payments']);
        $user = Auth::user();
        $companies = Company::all();
        $mod = new Supplier();
        $supplier_company = $name = $phone_number = $company_id = '';
        if($user->company){
            $company_id = $user->company_id;            
        }else{
            if ($request->get('company_id') != ""){
                $company_id = $request->get('company_id');
            }
        }  
        $data = $mod->orderBy('created_at', 'desc')->get();
        return view('concurrent_payments.index', compact('data', 'companies', 'company_id'));
    }

    public function supplier_purchases($id){
        config(['site.page' => 'concurrent_payments']);
        $user = Auth::user();
        $supplier = Supplier::find($id);
        $mod = $supplier->purchases()->where('status', 1);
        if($user->company){
            $mod = $mod->where('company_id', $user->company_id);
        }
        $data = $mod->orderBy('timestamp', 'desc')->get();
        return view('concurrent_payments.purchases', compact('data', 'supplier'));
    }

    public function add_payments($id){
        $user = Auth::user();
        $supplier = Supplier::find($id);
        $purchases = $supplier->purchases()->where('status', 1)->get();
        foreach ($purchases as $purchase) {
            $paid = $purchase->payments()->sum('amount');
            $grand_total = $purchase->grand_total;
            $balance = $grand_total - $paid;
            if($balance <= 0) continue;
            $payment = new Payment();
            $payment->timestamp = date('Y-m-d H:i:s');
            $payment->reference_no = "Concurrent Payment";
            $payment->amount = $balance;
            if($user->hasRole('secretary')){
                $payment->status = 0;
            }else{
                $payment->status = 1;
            }
            $payment->paymentable_id = $purchase->id;
            $payment->paymentable_type = 'App\Models\Purchase';
            $payment->save();
        }
        return back()->with('success', __('page.added_successfully'));
    }

    public function add_payments_post(Request $request, $id) {
        $supplier = Supplier::find($id);
        $filenames = [];
        if($request->file('attachment')) {
            $supplier_company = $supplier->company;
            $date_time = date('Y-m-d-H-i-s');
            $attach_name = $request->get('reference_no'). "_" . $supplier_company . "_" . $date_time;
            foreach ($request->file('attachment') as $key => $picture) {
                $imageName = $attach_name . $key . '.' . $picture->getClientOriginalExtension();
                $picture->move(public_path('images/uploaded/payment_images/'), $imageName);
                array_push($filenames, 'images/uploaded/payment_images/'.$imageName);
            }
        }
        $index_array = $request->get('checked');
        for ($i=0; $i < count($index_array); $i++) { 
            $index = $index_array[$i];
            $purchase_id = $request->get('purchase_id')[$index];
            $amount = $request->get('amount')[$index];
            $purchase = Purchase::find($purchase_id);
            $paid = $purchase->payments()->sum('amount');            
            $grand_total = $purchase->grand_total;
            $balance = $grand_total - $paid;
            if($balance <= 0) continue;
            $payment = new Payment();
            $payment->timestamp = $request->get('date') . ":00";
            $payment->reference_no = $request->get('reference_no');
            $payment->amount = $amount;
            if(Auth::user()->hasRole('secretary')){
                $payment->status = 0;
            }else{
                $payment->status = 1;
            }
            $payment->note = $request->get('note');
            $payment->paymentable_id = $purchase_id;
            $payment->paymentable_type = 'App\Models\Purchase';
            $payment->save();
            for ($i=0; $i < count($filenames); $i++) { 
                Image::create([
                    'imageable_id' => $payment->id,
                    'imageable_type' => 'App\Models\Payment',
                    'path' => $filenames[$i],
                ]);
            }
        }
        return back()->with('success', __('page.added_successfully'));
    }
}
