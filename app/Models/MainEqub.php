<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainEqub extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status', 'active', 'created_by', 'image', 'remark'
    ];

    public function subEqub() 
    {
        return $this->hasMany(EqubType::class, 'main_equb_id');
    }
    

}
