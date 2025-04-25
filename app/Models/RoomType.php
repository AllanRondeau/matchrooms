<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hotel_id', 'name', 'description', 'base_price', 'capacity', 'stock_quantity', 'amenities', 'images', 'status'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'base_price' => 'decimal:2',
        'capacity' => 'integer',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the hotel that owns the room type.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the negotiations for the room type.
     */
    public function negotiations()
    {
        return $this->hasMany(Negotiation::class);
    }

    /**
     * Get the reservations for the room type.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get the negotiation settings for the room type.
     */
    public function negotiationSettings()
    {
        return $this->hasMany(NegotiationSetting::class);
    }

    /**
     * Get the blackout periods for the room type.
     */
    public function blackoutPeriods()
    {
        return $this->hasMany(BlackoutPeriod::class);
    }

    /**
     * Check if negotiation is allowed for this room type on the given dates.
     */
    public function isNegotiationAllowed($checkInDate, $checkOutDate)
    {
        // Get global hotel setting
        $hotelSetting = $this->hotel->negotiationSettings()
            ->whereNull('room_type_id')
            ->first();
            
        // Get room-specific setting (overrides hotel setting if exists)
        $roomSetting = $this->negotiationSettings()->first();
        
        // Determine if negotiation is enabled
        $negotiationEnabled = $roomSetting ? $roomSetting->negotiation_enabled : 
            ($hotelSetting ? $hotelSetting->negotiation_enabled : true);
            
        if (!$negotiationEnabled) {
            return false;
        }
        
        // Check for blackout periods
        $blackoutCount = $this->hotel->blackoutPeriods()
            ->where(function($query) use ($checkInDate, $checkOutDate) {
                $query->where(function($q) use ($checkInDate, $checkOutDate) {
                    // Blackout period overlaps with stay
                    $q->where('start_date', '<=', $checkOutDate)
                      ->where('end_date', '>=', $checkInDate);
                });
            })
            ->count();
            
        if ($blackoutCount > 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Get available quantity for given dates.
     */
    public function getAvailableQuantity($checkInDate, $checkOutDate)
    {
        $reservedCount = $this->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($checkInDate, $checkOutDate) {
                $query->where(function($q) use ($checkInDate, $checkOutDate) {
                    // Reservation overlaps with requested dates
                    $q->where('check_in_date', '<', $checkOutDate)
                      ->where('check_out_date', '>', $checkInDate);
                });
            })
            ->count();
            
        return $this->stock_quantity - $reservedCount;
    }
}