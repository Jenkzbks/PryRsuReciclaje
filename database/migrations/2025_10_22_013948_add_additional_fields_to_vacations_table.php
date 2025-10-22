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
        Schema::table('vacations', function (Blueprint $table) {
            // Agregar tipo de vacaciones
            $table->enum('vacation_type', ['annual', 'personal', 'sick', 'maternity', 'paternity', 'emergency'])
                  ->default('annual')
                  ->after('employee_id');
            
            // Agregar empleado de reemplazo
            $table->unsignedBigInteger('replacement_employee_id')->nullable()->after('vacation_type');
            $table->foreign('replacement_employee_id')->references('id')->on('employee');
            
            // Agregar campo de razón/motivo
            $table->text('reason')->nullable()->after('replacement_employee_id');
            
            // Agregar días tomados (calculado)
            $table->integer('days_taken')->default(0)->after('requested_days');
            
            // Cambiar status para usar valores en minúsculas
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])
                  ->default('pending')
                  ->change();
            
            // Agregar campos de aprobación
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->foreign('approved_by')->references('id')->on('employee');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropForeign(['replacement_employee_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'vacation_type',
                'replacement_employee_id', 
                'reason',
                'days_taken',
                'approved_by',
                'approved_at'
            ]);
        });
    }
};
