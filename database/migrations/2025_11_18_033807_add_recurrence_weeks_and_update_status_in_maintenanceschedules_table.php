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
            // Agregar la columna recurrence_weeks
            $table->integer('recurrence_weeks')->default(1)->after('maintenance_type');
            
            // Actualizar el enum status para usar valores en inglés
            $table->dropColumn('status');
        });
        
        // Re-crear la columna status con los nuevos valores
        Schema::table('maintenanceschedules', function (Blueprint $table) {
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenanceschedules', function (Blueprint $table) {
            // Eliminar la columna recurrence_weeks
            $table->dropColumn('recurrence_weeks');
            
            // Volver al enum original en español
            $table->dropColumn('status');
        });
        
        // Re-crear la columna status con los valores originales
        Schema::table('maintenanceschedules', function (Blueprint $table) {
            $table->enum('status', ['programado', 'en_progreso', 'completado', 'cancelado'])->default('programado');
        });
    }
};
