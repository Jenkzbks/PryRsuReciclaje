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
        Schema::create('zoneshift', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shiftid');
            $table->unsignedBigInteger('zoneid');
            $table->foreign('zoneid')->references('id')->on('zones');
            $table->foreign('shiftid')->references('id')->on('shift');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoneshift');
    }
};
