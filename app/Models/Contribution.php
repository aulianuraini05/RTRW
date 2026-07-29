<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $fillable = [
        'contribution_type',
        'contribution_period',
        'amount',
        'payment_status',
        'peyment_date',
    ]
}
