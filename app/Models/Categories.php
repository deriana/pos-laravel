<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categories extends Model
{
    protected $guarded = 'id';
    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->HasMany(Products::class);
    }
}
