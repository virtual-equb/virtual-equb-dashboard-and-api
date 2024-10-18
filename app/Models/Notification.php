<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'body', 'equb_type_id', 'method', 'phone'
    ];
    public function equbType()
    {
        return $this->belongsTo(EqubType::class);
    }
}
