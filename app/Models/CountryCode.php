<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'icon',
        'active'
    ];

    public function country() {
        return $this->belongsTo(Country::class, 'id');
    }
}
