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
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->decimal('initial_offer', 10, 2);
            $table->datetime('offer_date');
            $table->datetime('expiry_date')->comment('3 hours from offer for hotelier to respond');
            $table->string('status')->default('pending')->comment('pending, accepted, rejected, counter_offered, expired');
            $table->string('hotelier_response')->nullable();
            $table->decimal('counter_offer', 10, 2)->nullable();
            $table->datetime('counter_offer_date')->nullable();
            $table->datetime('counter_offer_expiry')->nullable()->comment('24 hours from counter offer for user to respond');
            $table->string('user_response')->nullable()->comment('accepted, rejected, expired');
            $table->decimal('final_price', 10, 2)->nullable();
            $table->datetime('completed_date')->nullable();
            $table->timestamps();
            
            // Add an index to improve query performance 
            $table->index(['status', 'offer_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};