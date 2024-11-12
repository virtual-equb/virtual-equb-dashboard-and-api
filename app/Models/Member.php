<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'full_name', 
        'phone', 
        'gender', 
        'status',
        'email',
        'city',
        'subcity',
        'woreda',
        'house_number',
        'specific_location',
        // 'address',
        'profile_photo_path',
        'verified',
        'approved_by',
        'approved_date',
        'remark',
        'rating',
        'date_of_birth'
    ];

    public function equbs()
    {
        return $this->hasMany(Equb::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function equbTakers()
    {
        return $this->hasMany(EqubTaker::class);
    }
    public function memberCity() 
    {
        return $this->belongsTo(Cities::class, 'city');
    }
    public function memberSubcity()
    {
        return $this->belongsTo(Sub_city::class, 'subcity');
    }
    public function memberApproval()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
