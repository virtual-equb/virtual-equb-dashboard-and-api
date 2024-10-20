<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCity extends Model
{
    use HasFactory;

    protected $table = 'sub_cities'; // Specify the table name if it doesn't follow Laravel conventions

    protected $fillable = [
        'name',
        'active',
        'remark',
        'created_by',
        'city_id',
    ];

    /**
     * Get the user that created the sub city.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the city that the sub city belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}