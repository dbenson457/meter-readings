<?php

namespace Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('mpxn')->unique(); // Unique MPXN identifier
            $table->string('type'); // Type of meter (electricity or gas)
            $table->date('installation_date'); // Installation date of the meter
            $table->integer('estimated_annual_consumption'); // Estimated annual consumption
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meters'); // Drop the meters table
    }
}
