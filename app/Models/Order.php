<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    protected $guarded = [];

    public function orderable(){
        return $this->morphTo();
    }

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }
}
