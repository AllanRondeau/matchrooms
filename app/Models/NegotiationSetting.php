<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegotiationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'min_acceptance_threshold',
        'auto_reject_threshold',
        'negotiation_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_acceptance_threshold' => 'decimal:2',
        'auto_reject_threshold' => 'decimal:2',
        'negotiation_enabled' => 'boolean',
    ];

    /**
     * Get the hotel that owns the negotiation setting.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Get the room type that owns the negotiation setting.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Check if an offer should be auto-accepted.
     */
    public function shouldAutoAccept($offerPrice, $basePrice)
    {
        $offerPercentage = ($offerPrice / $basePrice) * 100;
        return $offerPercentage >= $this->min_acceptance_threshold;
    }

    /**
     * Check if an offer should be auto-rejected.
     */
    public function shouldAutoReject($offerPrice, $basePrice)
    {
        $offerPercentage = ($offerPrice / $basePrice) * 100;
        return $offerPercentage <= $this->auto_reject_threshold;
    }
}