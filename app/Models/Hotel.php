<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'city',
        'country',
        'postal_code',
        'star_rating',
        'amenities',
        'images',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'star_rating' => 'integer',
    ];

    /**
     * Get the hotelier that owns the hotel.
     */
    public function hotelier()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room types for the hotel.
     */
    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    /**
     * Get the negotiation settings for the hotel.
     */
    public function negotiationSettings()
    {
        return $this->hasMany(NegotiationSetting::class);
    }

    /**
     * Get the blackout periods for the hotel.
     */
    public function blackoutPeriods()
    {
        return $this->hasMany(BlackoutPeriod::class);
    }

    /**
     * Get all reviews for the hotel.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the badges awarded to the hotel.
     */
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'hotel_badges')
            ->withPivot('awarded_at', 'progress')
            ->withTimestamps();
    }

    /**
     * Get all reservations for this hotel through its room types.
     */
    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, RoomType::class);
    }

    /**
     * Get the average rating for this hotel.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }
}
