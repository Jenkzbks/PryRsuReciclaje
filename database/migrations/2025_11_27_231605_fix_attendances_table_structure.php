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
            // Verificar si las columnas existen antes de agregarlas
            if (!Schema::hasColumn('attendances', 'period')) {
                $table->integer('period')->default(1)->after('date');
            }
            
            // Hacer nullable los campos opcionales si no lo están
            if (Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->change();
            } else {
                $table->text('notes')->nullable()->after('status');
            }
            
            if (Schema::hasColumn('attendances', 'check_in')) {
                $table->dateTime('check_in')->nullable()->change();
            }
            
            if (Schema::hasColumn('attendances', 'check_out')) {
                $table->dateTime('check_out')->nullable()->change();
            }
            
            if (Schema::hasColumn('attendances', 'hours_worked')) {
                $table->decimal('hours_worked', 4, 2)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revertir cambios si es necesario
            if (Schema::hasColumn('attendances', 'period')) {
                $table->dropColumn('period');
            }
        });
    }
};
