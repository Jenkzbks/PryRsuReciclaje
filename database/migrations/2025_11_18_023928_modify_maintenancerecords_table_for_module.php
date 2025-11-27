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
        Schema::table('maintenancerecords', function (Blueprint $table) {
            // Agregar las columnas necesarias para el módulo
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained('employee')->onDelete('set null');
            $table->text('activity_description')->nullable();
            $table->datetime('activity_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('image_path')->nullable();
            
            // Hacer opcionales las columnas existentes
            $table->unsignedBigInteger('schedule_id')->nullable()->change();
            $table->date('maintenance_date')->nullable()->change();
            $table->string('descripcion')->nullable()->change();
            $table->string('image_url', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenancerecords', function (Blueprint $table) {
            $table->dropForeign(['maintenance_id']);
            $table->dropForeign(['employee_id']);
            $table->dropColumn([
                'maintenance_id',
                'employee_id', 
                'activity_description', 
                'activity_date', 
                'notes',
                'image_path'
            ]);
        });
    }
};
