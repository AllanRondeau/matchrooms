<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Negotiation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'room_type_id',
        'initial_offer',
        'offer_date',
        'expiry_date',
        'status',
        'hotelier_response',
        'counter_offer',
        'counter_offer_date',
        'counter_offer_expiry',
        'user_response',
        'final_price',
        'completed_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'initial_offer' => 'decimal:2',
        'counter_offer' => 'decimal:2',
        'final_price' => 'decimal:2',
        'offer_date' => 'datetime',
        'expiry_date' => 'datetime',
        'counter_offer_date' => 'datetime',
        'counter_offer_expiry' => 'datetime',
        'completed_date' => 'datetime',
    ];

    /**
     * Get the user that owns the negotiation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the room type that the negotiation is for.
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the reservation that resulted from this negotiation.
     */
    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    /**
     * Check if the negotiation has expired.
     */
    public function isExpired()
    {
        if ($this->status === 'pending' && $this->expiry_date < Carbon::now()) {
            return true;
        }

        if ($this->status === 'counter_offered' && $this->counter_offer_expiry < Carbon::now()) {
            return true;
        }

        return false;
    }

    /**
     * Auto-process the negotiation based on settings.
     */
    public function autoProcess()
    {
        // Get negotiation settings
        $roomType = $this->roomType;
        $settings = $roomType->negotiationSettings()->first() ?? 
                   $roomType->hotel->negotiationSettings()->whereNull('room_type_id')->first();

        if (!$settings) {
            return;
        }

        $basePrice = $roomType->base_price;
        
        if ($settings->shouldAutoAccept($this->initial_offer, $basePrice)) {
            $this->status = 'accepted';
            $this->hotelier_response = 'auto_accepted';
            $this->final_price = $this->initial_offer;
            $this->completed_date = Carbon::now();
            $this->save();
            return;
        }
        
        if ($settings->shouldAutoReject($this->initial_offer, $basePrice)) {
            $this->status = 'rejected';
            $this->hotelier_response = 'auto_rejected';
            $this->completed_date = Carbon::now();
            $this->save();
            return;
        }
    }

    /**
     * Handle negotiation expiry.
     */
    public function handleExpiry()
    {
        if (!$this->isExpired()) {
            return;
        }

        if ($this->status === 'pending') {
            $this->status = 'expired';
            $this->hotelier_response = 'no_response';
            $this->completed_date = Carbon::now();
            $this->save();
        } elseif ($this->status === 'counter_offered') {
            $this->status = 'expired';
            $this->user_response = 'no_response';
            $this->completed_date = Carbon::now();
            $this->save();
        }
    }
}