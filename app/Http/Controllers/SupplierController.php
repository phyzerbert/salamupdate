<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Payment;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReportMail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupplierExport;

use Auth;
use PDF;

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
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }

    
    public function report($id){
        $supplier = Supplier::find($id);
        $pdf = PDF::loadView('reports.suppliers_report.pdf', compact('supplier'));        
        return $pdf->download('supplier_report_'.$supplier->company.'.pdf');        
        // return view('reports.suppliers_report.pdf', compact('supplier'));
    }
    
    public function email($id){
        $supplier = Supplier::find($id);
        $pdf = PDF::loadView('reports.suppliers_report.pdf', compact('supplier'));   
        if(filter_var($supplier->email, FILTER_VALIDATE_EMAIL)){
            $to_email = $supplier->email;
            Mail::to($to_email)->send(new ReportMail($pdf, 'Supplier Report'));
            return back()->with('success', 'Email is sent successfully');
        }else{
            return back()->withErrors('email', 'Supplier email address is invalid.');
        }
    }

    public function export($id) {        
        $supplier = Supplier::find($id);
        $purchases = $supplier->purchases;
        $purchase_array = $supplier->purchases->pluck('id');
        $payments = Payment::whereIn('paymentable_id', $purchase_array)->where('paymentable_type', 'App\Models\Purchase')->get();
        return Excel::download(new SupplierExport($purchases, $payments), 'supplier_report.xlsx');
    }
}
