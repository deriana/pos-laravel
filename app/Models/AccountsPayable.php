<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountsPayable extends Model
{
    use HasFactory;

    protected $table = 'accounts_payable';

    protected $fillable = [
        'supplier_id', 'purchase_id', 'amount_due', 'amount_paid', 'due_date', 'payment_method', 'status',
    ];

    public $timestamps = false; 

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
