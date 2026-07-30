<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = [
        'transaction_type',
        'description',
        'amount',
        'transaction_date',
    ];
}
