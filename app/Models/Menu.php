<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'description', 'price', 'image', 'category', 'is_available'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
