<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'negotiation_id',
        'user_id',
        'room_type_id',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'final_price',
        'status',
        'reservation_date',
        'special_requests',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'guests_count' => 'integer',
        'final_price' => 'decimal:2',
        'reservation_date' => 'datetime',
    ];

    /**
     * Get the user that owns the reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room type that the reservation is for.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the negotiation that led to this reservation.
     */
    public function negotiation()
    {
        return $this->belongsTo(Negotiation::class);
    }

    /**
     * Get the payments for this reservation.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the review for this reservation.
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get the number of nights for this reservation.
     */
    public function getNightsAttribute()
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Check if the reservation is fully paid.
     */
    public function isFullyPaid()
    {
        $totalPaid = $this->payments()->where('status', 'completed')->sum('amount');
        return $totalPaid >= $this->final_price;
    }

    /**
     * Check if the reservation can be cancelled.
     */
    public function canBeCancelled()
    {
        return $this->status === 'pending' || $this->status === 'confirmed';
    }
}