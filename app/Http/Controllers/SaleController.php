<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Company;
use App\Models\Store;
use App\User;
use App\Models\StoreProduct;

use Auth;   

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        config(['site.page' => 'sale_list']);
        $user = Auth::user();
        $stores = Store::all();
        $customers = Customer::all();
        $companies = Company::all();

        $mod = new Sale();
        if($user->role->slug == 'user'){
            $mod = $user->company->sales();
            $stores = $user->company->stores;
        }

        $company_id = $reference_no = $customer_id = $store_id = $period = '';
        if ($request->get('company_id') != ""){
            $company_id = $request->get('company_id');
            $mod = $mod->where('company_id', $company_id);
        }
        if ($request->get('reference_no') != ""){
            $reference_no = $request->get('reference_no');
            $mod = $mod->where('reference_no', 'LIKE', "%$reference_no%");
        }
        if ($request->get('customer_id') != ""){
            $customer_id = $request->get('customer_id');
            $mod = $mod->where('customer_id', $customer_id);
        }
        if ($request->get('store_id') != ""){
            $store_id = $request->get('store_id');
            $mod = $mod->where('store_id', $store_id);
        }
        if ($request->get('period') != ""){   
            $period = $request->get('period');
            $from = substr($period, 0, 10);
            $to = substr($period, 14, 10);
            $mod = $mod->whereBetween('timestamp', [$from, $to]);
        }
        $pagesize = session('pagesize');

        $data = $mod->orderBy('created_at', 'desc')->paginate($pagesize);
        return view('sale.index', compact('data', 'companies', 'stores', 'customers', 'company_id', 'store_id', 'customer_id', 'reference_no', 'period'));
    }

    public function create(Request $request){
        config(['site.page' => 'sale_create']); 
        $user = Auth::user();
        $customers = Customer::all();
        $products = Product::all();
        $stores = Store::all();
        if($user->hasRole('user')){
            $stores = $user->company->stores;
        }
        $users = User::where('role_id', 2)->get();
        return view('sale.create', compact('customers', 'stores', 'products', 'users'));
    }

    public function save(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_number'=>'required|string',
            'store'=>'required',
            'customer'=>'required',
            'user'=>'required',
            'status'=>'required',
        ]);

        $data = $request->all();
        // dd($data);
        $item = new Sale();
        $item->user_id = Auth::user()->id;
        $item->biller_id = $data['user'];
        $item->timestamp = $data['date'].":00";
        $item->reference_no = $data['reference_number'];
        $item->store_id = $data['store'];
        $store = Store::find($data['store']);
        $item->company_id = $store->company_id;
        $item->customer_id = $data['customer'];
        $item->status = $data['status'];

        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "sale_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/sale_images/'), $imageName);
            $item->attachment = 'images/uploaded/sale_images/'.$imageName;
        }
        $item->save();

        for ($i=0; $i < count($data['product_id']); $i++) {             
            
            $store_product = StoreProduct::where('store_id', $data['store'])->where('product_id', $data['product_id'][$i])->first();
            if(isset($store_product)){
                if($store_product->quantity < $data['quantity'][$i]){
                    continue;
                }
                $store_product->decrement('quantity', $data['quantity'][$i]);
            }else{
                continue;
            }

            Order::create([
                'product_id' => $data['product_id'][$i],
                'price' => $data['price'][$i],
                'quantity' => $data['quantity'][$i],
                'subtotal' => $data['subtotal'][$i],
                'orderable_id' => $item->id,
                'orderable_type' => Sale::class,
            ]);

        }

        return back()->with('success', 'Created Successfully');
    }

    public function edit(Request $request, $id){    
        config(['site.page' => 'sale']);
        $user = Auth::user();
        $users = User::where('role_id', 2)->get();
        $sale = Sale::find($id);        
        $customers = Customer::all();
        $products = Product::all();
        $stores = Store::all();
        if($user->role->slug == 'user'){
            $stores = $user->company->stores;
        }

        return view('sale.edit', compact('sale', 'users', 'customers', 'stores', 'products'));
    }

    public function detail(Request $request, $id){    
        config(['site.page' => 'sale']);    
        $sale = Sale::find($id);

        return view('sale.detail', compact('sale'));
    }

    public function update(Request $request){
        $request->validate([
            'date'=>'required|string',
            'reference_number'=>'required|string',
            'store'=>'required',
            'customer'=>'required',
            'user'=>'required',
            'status'=>'required',
        ]);
        $data = $request->all();
        // dd($data);
        $item = Sale::find($request->get("id"));
 
        $item->biller_id = $data['user'];  
        $item->timestamp = $data['date'].":00";
        $item->reference_no = $data['reference_number'];
        $item->store_id = $data['store'];
        $store = Store::find($data['store']);
        $item->company_id = $store->company_id;
        $item->customer_id = $data['customer'];
        $item->status = $data['status'];
        $item->note = $data['note'];

        if($request->has("attachment")){
            $picture = request()->file('attachment');
            $imageName = "sale_".time().'.'.$picture->getClientOriginalExtension();
            $picture->move(public_path('images/uploaded/sale_images/'), $imageName);
            $item->attachment = 'images/uploaded/sale_images/'.$imageName;
        }

        for ($i=0; $i < count($data['order_id']); $i++) { 
            $order = Order::find($data['order_id'][$i]);
            $order_original_quantity = $order->quantity;
            $order->update([
                'product_id' => $data['product_id'][$i],
                'price' => $data['price'][$i],
                'quantity' => $data['quantity'][$i],
                'subtotal' => $data['subtotal'][$i],
            ]);
            if($order_original_quantity != $data['quantity'][$i]){
                $store_product = StoreProduct::where('store_id', $data['store'])->where('product_id', $data['product_id'][$i])->first();                
                $store_product->increment('quantity', $order_original_quantity);
                $store_product->decrement('quantity', $data['quantity'][$i]);
            }
        }

        $item->save();
        return back()->with('success', 'Updated Successfully');
    }

    public function delete($id){
        $item = Sale::find($id);
        $item->orders()->delete();
        $item->payments()->delete();
        $item->delete();
        return back()->with("success", __('page.deleted_successfully'));
    }
}
