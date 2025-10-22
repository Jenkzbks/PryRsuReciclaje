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
        Schema::table('attendances', function (Blueprint $table) {
            // Primero eliminar la foreign key existente
            $table->dropForeign(['employee_id']);
            
            // Crear la foreign key correcta que referencia a la tabla 'employees'
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revertir: eliminar la foreign key correcta y recrear la incorrecta
            $table->dropForeign(['employee_id']);
            $table->foreign('employee_id')->references('id')->on('employee');
        });
    }
};
