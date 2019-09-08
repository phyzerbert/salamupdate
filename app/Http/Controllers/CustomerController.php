<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        config(['site.page' => 'customer']);
        $mod = new Customer();
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
        return view('admin.customers', compact('data', 'company', 'name', 'phone_number'));
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required',
        ]);
        $item = Customer::find($request->get("id"));
        $item->name = $request->get("name");
        $item->company = $request->get("company");
        $item->email = $request->get("email");
        $item->phone_number = $request->get("phone_number");
        $item->address = $request->get("address");
        $item->city = $request->get("city");
        $item->save();
        return response()->json('success');
    }

    public function create(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        
        Customer::create([
            'name' => $request->get('name'),
            'company' => $request->get('company'),
            'email' => $request->get('email'),
            'phone_number' => $request->get('phone_number'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
        ]);
        return response()->json('success');
    }

    public function delete($id){
        $item = Customer::find($id);
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
