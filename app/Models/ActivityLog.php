<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'type_id', 'action', 'user_id', 'username', 'role', 'type_name', 'gender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'type_id');
    }
    public function equbType()
    {
        return $this->belongsTo(EqubType::class, 'type_id');
    }
    public function equbTaker()
    {
        return $this->belongsTo(EqubTaker::class, 'type_id');
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'type_id');
    }
    public function equb()
    {
        return $this->belongsTo(Equb::class, 'type_id');
    }

    public function getTypeAttribute($value)
    {
        switch ($value) {
            case 'users':
                return "Users";
            case 'members':
                return "Members";
            case 'equb_types':
                return "Equb Types";
            case 'equb_takers':
                return "Lottery winers";
            case 'payments':
                return "Payments";
            case 'equbs':
                return "Equbs";
            case 'rejected_dates':
                return "Off Dates";
            case 'notification':
                return "Notifications";
            default:
                return;
        }
    }
}
