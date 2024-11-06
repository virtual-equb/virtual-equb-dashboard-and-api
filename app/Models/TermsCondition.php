<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    use HasFactory;

    // Specify the table name if it doesn't follow Laravel's naming convention
    protected $table = 'terms_conditions';

    // Specify the fillable fields for mass assignment
    protected $fillable = [
        'title',
        'content',
    ];
}