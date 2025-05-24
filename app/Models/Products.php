<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use SoftDeletes;

    protected $guarded = 'id';
    protected $fillable = ['category_id', 'name', 'sku', 'product_image', 'purchase_price', 'selling_price', 'stock', 'unit'];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
    public function barCode()
    {
        return $this->hasOne(BarCode::class, 'product_id');
    }
    
}
