<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id(); // Primary key
            // Foreign key to meters table, with cascade on delete
            $table->foreignId('meter_id')->constrained()->onDelete('cascade');
            $table->integer('reading_value'); // Reading value
            $table->date('reading_date'); // Date of the reading
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_readings'); // Drop the meter_readings table
    }
};
