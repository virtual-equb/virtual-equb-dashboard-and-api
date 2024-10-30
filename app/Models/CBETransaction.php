<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBETransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'enc_val',
        'transaction_id',
        'state',
        'tnd_date',
        'signature'
    ];
}
