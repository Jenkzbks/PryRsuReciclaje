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
        Schema::table('contrato', function (Blueprint $table) {
            // Hacer end_date nullable para contratos permanentes
            $table->date('end_date')->nullable()->change();
            
            // Hacer termination_reason nullable
            $table->string('termination_reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrato', function (Blueprint $table) {
            $table->date('end_date')->nullable(false)->change();
            $table->string('termination_reason')->nullable(false)->change();
        });
    }
};