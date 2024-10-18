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

    public function cityCountry()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subCity() 
    {
        return $this->belongsTo(Sub_city::class, 'id');
    }
}
