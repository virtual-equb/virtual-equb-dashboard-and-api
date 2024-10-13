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
        'name', 'round', 'status', 'remark', 'lottery_date',
        'rote', 'type', 'terms', 'quota', 'start_date', 'end_date', 'remaining_quota', 'image'
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
    //   public function equbType(){
    //     return $this->belongsTo(Member::class);
    // }

}
