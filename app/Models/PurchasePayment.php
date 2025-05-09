<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'amount', 'payment_date', 'payment_method', 'note',
    ];
    protected $casts = [
        'payment_date' => 'datetime',
    ];
    public $timestamps = false; 
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
