<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Order;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Image;

use App;

class VueController extends Controller
{
    
    public function get_products() {
        $products = Product::all();

        return response()->json($products);
    }

    public function get_product(Request $request) {
        $id = $request->get('id');

        $product = Product::find($id)->load('tax');

        return response()->json($product);
    }

    public function get_orders(Request $request) {
        $id = $request->get('id');
        $type = $request->get('type');
        // dd($request->all());
        if($type == 'purchase'){
            $item = Purchase::find($id);
        }elseif($type == 'sale'){
            $item = Sale::find($id);
        }        
        $orders = $item->orders;
        return response()->json($orders);
    }

    public function get_data(Request $request){
        $id = $request->get('id');
        $type = $request->get('type');
        // dd($request->all());
        if($type == 'purchase'){
            $item = Purchase::find($id);
        }elseif($type == 'sale'){
            $item = Sale::find($id);
        }
        return response()->json($item);
    }

    public function get_first_product(Request $request){
        $item = Product::with('tax')->first();
        return response()->json($item);
    }

    public function get_autocomplete_products(Request $request){
        $keyword = $request->get('keyword');
        $data = Product::with('tax')->where('name', 'LIKE', "%$keyword%")->orWhere('code', 'LIKE', "%$keyword%")->get();
        return response()->json($data);
    }

    public function get_pre_order(Request $request){
        $id = $request->get('id');
        $item = PreOrder::find($id)->load('items');
        return response()->json($item);
    }

    public function get_received_quantity(Request $request){
        $id = $request->get('id');
        $item = PreOrderItem::find($id);
        $received_quantity = $item->purchased_items->sum('quantity');
        return response()->json($received_quantity);
    }

    public function image_migrate(){
        ini_set('max_execution_time', '900000000');
        $data = Payment::all();
        foreach ($data as $item) {
            if($item->attachment){                
                Image::create([
                    'imageable_id' => $item->id,
                    'imageable_type' => 'App\Models\Payment',
                    'path' => $item->attachment,
                ]);
            }
        }
        $data = Product::all();
        foreach ($data as $item) {
            if($item->image){                
                Image::create([
                    'imageable_id' => $item->id,
                    'imageable_type' => 'App\Models\Product',
                    'path' => $item->image,
                ]);
            }
        }
        dump('ok');
    }

    public function auth_check(Request $request) {
        $auth_id = $request->get('id');
        if($auth_id == auth()->id()) {
            return response()->json('success');
        } else {
            return response()->json('fail');
        }
    }
    
}
