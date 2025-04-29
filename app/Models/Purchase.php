<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'sale_date',
        'invoice_number',
        'total',
        'discount',
        'tax',
        'grand_total',
        'payment_status',
        'note',
    ];

    public function products()
    {
        return $this->belongsToMany(Products::class)->withPivot('quantity', 'price', 'subtotal');
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function accountsPayable()
    {
        return $this->hasMany(AccountsPayable::class);
    }
}
