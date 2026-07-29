<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketplace extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'price',
        'stock',
        'product_status',
    ];
}