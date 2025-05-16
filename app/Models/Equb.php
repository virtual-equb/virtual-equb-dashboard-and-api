<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equb extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id', 
        'equb_type_id', 
        'amount', 
        'total_amount', 
        'start_date', 
        'end_date', 
        'lottery_date', 
        'status', 
        'timeline', 
        'check_for_draw', 
        'notified'
    ];


    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    public function equbType()
    {
        return $this->belongsTo(EqubType::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function equbTakers()
    {
        return $this->hasMany(EqubTaker::class)
        ->where('payment_type', '!=', '')
        ->whereNotIn('status', ['pending', 'void']);
    }
}
