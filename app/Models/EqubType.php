<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EqubType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 
        'round', 
        'amount', 
        'total_amount',
        'total_members', 
        'expected_members', 
        'status', 
        'remark', 
        'lottery_date',
        'rote', 
        'type', 
        'terms', 
        'quota', 
        'start_date', 
        'end_date', 
        'remaining_quota', 
        'image', 
        'main_equb_id',
        'lottery_round'
    ];

    public function equbs()
    {
        return $this->hasMany(Equb::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function mainEqub()
  {
    return $this->belongsTo(MainEqub::class, 'main_equb_id');  // Ensure the foreign key is correct
  }

}
