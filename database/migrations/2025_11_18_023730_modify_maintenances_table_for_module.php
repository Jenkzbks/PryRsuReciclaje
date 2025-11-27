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
        Schema::table('maintenances', function (Blueprint $table) {
            // Agregar las columnas necesarias para el módulo de mantenimiento
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('cascade');
            $table->string('maintenance_type')->nullable(); // Preventivo, Correctivo, Inspección
            $table->text('description')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->enum('status', ['programado', 'en_progreso', 'completado', 'cancelado'])->default('programado');
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn([
                'vehicle_id', 
                'maintenance_type', 
                'description', 
                'scheduled_date', 
                'completed_date', 
                'status', 
                'cost', 
                'notes'
            ]);
        });
    }
};
