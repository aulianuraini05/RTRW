<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillablefillable = [
        'asset_name',
        'asset_type',
        'quantity',
        'condition',
        'descrition',
    ];
}
