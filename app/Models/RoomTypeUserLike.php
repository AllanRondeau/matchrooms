<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeUserLike extends Model
{
    protected $fillable = [
        'user_id',
        'room_type_id',
    ];
    public $timestamps = false;

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
