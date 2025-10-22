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
        Schema::create('employeegroups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->foreign('shift_id')->references('id')->on('shift');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->string('days', 255);
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeegroups');
    }
};
