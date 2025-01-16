<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('meters', function (Blueprint $table) {
            $table->id();
            $table->string('mpxn')->unique();
            $table->date('installation_date');
            $table->enum('type', ['electricity', 'gas']);
            $table->integer('estimated_annual_consumption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
