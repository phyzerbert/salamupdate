<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
 
    protected $fillable = [
        'timestamp', 'reference_no', 'store_id', 'company_id', 'supplier_id', 'discount', 'shipping', 'returns', 'grand_total', 'credit_days', 'expiry_date', 'attachment', 'note', 'status',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function orders()
    {
        return $this->morphMany('App\Models\Order', 'orderable');
    }

    public function payments()
    {
        return $this->morphMany('App\Models\Payment', 'paymentable');
    }

    public function store(){
        return $this->belongsTo('App\Models\Store');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company');
    }

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }
}
