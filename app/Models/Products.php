<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $guarded = 'id';
    protected $fillable = ['category_id', 'name', 'sku', 'product_image', 'purchase_price', 'selling_price', 'stock', 'unit'];

    // Di model Product
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
    public function qrCode()
    {
        return $this->hasOne(QrCode::class, 'product_id');
    }
    
}
