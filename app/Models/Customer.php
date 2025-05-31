<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
