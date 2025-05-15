<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_date',
        'invoice_number',
        'total',
        'discount',
        'tax',
        'grand_total',
        'payment_status',
        'note'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
    public function accountsReceivable()
    {
        return $this->hasMany(AccountsReceivable::class);
    }
}
