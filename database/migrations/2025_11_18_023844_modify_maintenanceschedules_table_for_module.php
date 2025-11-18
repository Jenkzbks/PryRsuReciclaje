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
        Schema::table('maintenanceschedules', function (Blueprint $table) {
            // Agregar las columnas necesarias
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['programado', 'en_progreso', 'completado', 'cancelado'])->default('programado');
            
            // Hacer opcionales las columnas existentes que pueden causar problemas
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();
            $table->unsignedBigInteger('driver_id')->nullable()->change();
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
            $table->string('day_of_week', 255)->nullable()->change();
            $table->string('maintenance_type', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenanceschedules', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'description', 'status']);
        });
    }
};
