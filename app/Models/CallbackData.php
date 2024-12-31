<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallbackData extends Model
{
    use HasFactory;

    protected $fillable = [
        'paidAmount',
        'paidByNumber',
        'transactionId',
        'transactionTime',
        'tillCode',
        'token',
        'signature'
    ];
}
