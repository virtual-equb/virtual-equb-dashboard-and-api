<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryWinner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'member_id', 'equb_type_id', 'member_name', 'equb_type_name'
    ];
}
