<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'equb_id',
        'payment_type',
        'amount',
        'creadit',
        'balance',
        'collecter',
        'status',
        'paid_date',
        'transaction_number',
        'note',
        'payment_proof',
        'msisdn',
        'tradeDate',
        'tradeNo',
        'tradeStatus',
        'signature',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function equb()
    {
        return $this->belongsTo(Equb::class);
    }
    public function collecter()
    {
        return $this->belongsTo(User::class, 'collecter', 'id');
    }
}
