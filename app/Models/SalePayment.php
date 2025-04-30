<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalePayment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['sale_id', 'amount', 'payment_date', 'payment_methode', 'note'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
