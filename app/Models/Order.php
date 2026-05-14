<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_name', 'table_number', 'notes', 'total_price', 'status', 'payment_method'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
