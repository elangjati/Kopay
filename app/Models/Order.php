<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_name', 'table_number', 'notes', 'total_price', 'status', 'payment_method'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
