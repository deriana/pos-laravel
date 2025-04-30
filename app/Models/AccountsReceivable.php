<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountsReceivable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'accounts_reveivable'; 

    protected $fillable = ['sale_id', 'amount', 'payment_method', 'note'];
}
