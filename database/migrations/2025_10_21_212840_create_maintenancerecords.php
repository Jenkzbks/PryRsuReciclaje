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
        Schema::create('maintenancerecords', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')->references('id')->on('maintenanceschedules');
            $table->date('maintenance_date');
            $table->string('descripcion');
            $table->boolean('estado')->default(false); // 0: no realizado, 1: realizado
            $table->string('image_url',255);
            $table->timestamps();
        });
    }

    /**    
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenancerecords');
    }
};
