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
        Schema::table('users', function (Blueprint $table) {
            // Rename name to first_name
            $table->renameColumn('name', 'first_name');
            
            // Add new columns
            $table->string('last_name')->after('first_name');
            $table->string('phone_number')->nullable()->after('profile_picture');
            $table->string('status')->default('active')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Undo the changes
            $table->renameColumn('first_name', 'name');
            $table->dropColumn(['last_name', 'phone_number', 'status']);
        });
    }
};