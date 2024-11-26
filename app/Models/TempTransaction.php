<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'member_id',
        'equb_id',
        'amount',
        'payment_type',
        'balance',
    ];
}
