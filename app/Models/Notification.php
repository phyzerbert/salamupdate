<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    protected $with = ['notifiable'];

    public function notifiable() {
        return $this->morphTo();
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
