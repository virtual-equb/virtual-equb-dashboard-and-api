<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_city extends Model
{
    use HasFactory;


    protected $fillable = [
        'created_by',
        'city_id',
        'name',
        'active',
        'remark'
    ];
}
