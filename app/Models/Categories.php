<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;

    protected $guarded = 'id';
    protected $fillable = ['name', 'description'];

    public function product()
    {
        return $this->HasMany(Products::class);
    }
}
