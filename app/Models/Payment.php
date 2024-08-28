<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['table_id', 'amount', 'status'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
