<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;


    protected $fillable = [
        'created_by',
        'country_id',
        'name',
        'active',
        'remark',
        'status'
    ];
}
