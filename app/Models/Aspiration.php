<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspiration extends Model
{
    protected $fillable = [
        'aspiration_title',
        'aspiration_content',
        'category',
        'submission_date',
        'aspiration_status',
    ];
}