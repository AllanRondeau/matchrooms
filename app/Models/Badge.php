<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'type',
        'criteria',
    ];

    /**
     * Get the users that have been awarded this badge.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('awarded_at', 'progress')
            ->withTimestamps();
    }

    /**
     * Get the hotels that have been awarded this badge.
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_badges')
            ->withPivot('awarded_at', 'progress')
            ->withTimestamps();
    }

    /**
     * Check if badge criteria is met for a user.
     */
    public function checkUserCriteria(User $user)
    {
        // This would be implemented based on the specific criteria
        // stored in the criteria field. For example:
        
        if ($this->criteria === 'reservations_count_10') {
            return $user->reservations()->count() >= 10;
        }
        
        if ($this->criteria === 'successful_negotiations_5') {
            return $user->negotiations()->where('status', 'accepted')->count() >= 5;
        }
        
        return false;
    }

    /**
     * Check if badge criteria is met for a hotel.
     */
    public function checkHotelCriteria(Hotel $hotel)
    {
        // Similar implementation based on hotel criteria
        
        if ($this->criteria === 'high_rating') {
            return $hotel->reviews()->avg('rating') >= 4.5;
        }
        
        if ($this->criteria === 'fast_response') {
            // Logic for fast negotiation response times
            return true;
        }
        
        return false;
    }
}