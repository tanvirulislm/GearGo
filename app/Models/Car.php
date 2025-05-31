<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $guarded = [];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->brand} {$this->model}";
    }
}
