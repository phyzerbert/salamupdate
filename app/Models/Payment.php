<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'timestamp', 'reference_no', 'amount', 'attachment', 'note', 'paymentable_id', 'paymentable_type',
    ];

    public function paymentable()
    {
        return $this->morphTo();
    }
}
