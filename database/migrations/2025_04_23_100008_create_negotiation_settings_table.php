<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('negotiation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('min_acceptance_threshold', 5, 2)->comment('Percentage of base price');
            $table->decimal('auto_reject_threshold', 5, 2)->comment('Percentage of base price');
            $table->boolean('negotiation_enabled')->default(true);
            $table->timestamps();
            
            // Add a unique constraint to ensure only one setting per hotel+room_type combo
            $table->unique(['hotel_id', 'room_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_settings');
    }
};