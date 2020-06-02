<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    public function paymentable()
    {
        return $this->morphTo();
    }
    
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

}
