<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EqubTaker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'equb_id', 'payment_type', 'amount', 'remaining_amount', 'status', 'paid_by', 'total_payment', 'remaining_payment', 'cheque_amount', 'cheque_bank_name', 'cheque_description', 'remark',
        'transaction_number',
        'paid_date'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function equb()
    {
        return $this->belongsTo(Equb::class);
    }
}
