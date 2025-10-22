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
            // Primero agregar los nuevos campos usando attendance_date como referencia
            $table->dateTime('check_in')->nullable()->after('attendance_date');
            $table->dateTime('check_out')->nullable()->after('check_in');
            $table->decimal('hours_worked', 4, 2)->default(0)->after('check_out');
            
            // Cambiar status de integer a string
            $table->string('status', 20)->change();
            
            // Modificar notes para que sea nullable
            $table->text('notes')->nullable()->change();
            
            // Eliminar campo period si no se necesita
            $table->dropColumn('period');
        });
        
        // En una segunda operación, renombrar la columna
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('attendance_date', 'date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revertir el renombre primero
            $table->renameColumn('date', 'attendance_date');
        });
        
        Schema::table('attendances', function (Blueprint $table) {
            // Luego revertir los otros cambios
            $table->integer('status')->change();
            $table->dropColumn(['check_in', 'check_out', 'hours_worked']);
            $table->string('notes')->change();
            $table->integer('period')->after('attendance_date');
        });
    }
};
