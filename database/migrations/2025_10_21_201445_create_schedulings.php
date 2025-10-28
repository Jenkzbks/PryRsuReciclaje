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
        Schema::create('schedulings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('zone_id');
            $table->foreign('group_id')->references('id')->on('employeegroups');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->date('date');
            $table->string('notes', 120);
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
