<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarCode  extends Model
{
    protected $fillable = [
        'product_id',
        'filename'
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    protected $table = 'barcode';
}
