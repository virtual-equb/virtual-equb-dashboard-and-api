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
        'full_name', 'phone', 'gender', 'status',
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
        'age'
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
    //   public function equbType(){
    //     return $this->hasMany(EqubType::class);
    // }
}
