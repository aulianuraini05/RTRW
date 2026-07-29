<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = [
        'letter_number',
        'letter_type',
        'submission_date',
        'letter_date',
        'purpose',
        'letter_status',
    ]
}