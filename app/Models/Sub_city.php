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
    ];

    public function city() {
        return $this->belongsTo(Cities::class, 'city_id');
    }

    public function subCreater()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('active', '1');
    }

    public function scopeInactive($query)
    {
        return $query->where('active', '0');
    }
}
