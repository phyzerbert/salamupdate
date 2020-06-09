<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    protected $fillable = [
        'name', 'code', 'barcode_symbology_id', 'category_id', 'unit', 'cost', 'price', 'tax_id', 'tax_method', 'alert_quantity', 'supplier_id', 'image', 'detail', 
    ];

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }

    public function barcode_symbology(){
        return $this->belongsTo('App\Models\BarcodeSymbology', 'barcode_symbology_id');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }

    public function stores(){
        return $this->belongsToMany('App\Models\Store', 'store_products');
    }

    public function store_products(){
        return $this->hasMany('App\Models\StoreProduct');
    }

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function calc_quantity(){
        $quantity_purchase = Order::where('product_id', $this->id)->where('orderable_type', 'App\Models\Purchase')->sum('quantity');
        $quantity_sale = Order::where('product_id', $this->id)->where('orderable_type', 'App\Models\Sale')->sum('quantity');
        return $quantity_purchase -  $quantity_sale;
    }
}
