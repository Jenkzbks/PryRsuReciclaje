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
        Schema::table('zones', function (Blueprint $table) {
            // Agregar province_id si no existe
            if (!Schema::hasColumn('zones', 'province_id')) {
                $table->unsignedBigInteger('province_id')->nullable()->after('district_id');
                $table->foreign('province_id')->references('id')->on('provinces');
            }
            
            // Hacer district_id nullable
            $table->unsignedBigInteger('district_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zones', function (Blueprint $table) {
            // Revertir district_id a NOT NULL (solo si no hay registros con district_id null)
            $table->unsignedBigInteger('district_id')->nullable(false)->change();
            
            // Eliminar province_id si existe
            if (Schema::hasColumn('zones', 'province_id')) {
                $table->dropForeign(['province_id']);
                $table->dropColumn('province_id');
            }
        });
    }
};
