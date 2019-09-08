<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;

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
        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "payment_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/payment_images/'), $imageName);
            $item->attachment = 'images/uploaded/payment_images/'.$imageName;
        }
        $item->save();
        return back()->with('success', 'Added Successfully');
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
        return back()->with('success', 'Updated Successfully');
    }


    public function delete($id){
        $item = Payment::find($id);
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
