<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

   protected $fillable = ['table_id', 'dish_id', 'quantity', 'price', 'status', 'comment', 'payment_status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
    public function addons()
    {
        return $this->belongsToMany(AddOn::class, 'order_add_ons', 'order_id', 'add_on_id');
    }
}
