<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountsReceivable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'accounts_receivable';

    protected $fillable = ['sale_id', 'customer_id', 'amount_due', 'amount_paid', 'status', 'due_date', 'payment_method', 'note'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
