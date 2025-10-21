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
        Schema::create('contrato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employee');
            $table->string('contrato_type',100);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('salary',10,2);
            $table->unsignedBigInteger('position_id');
            $table->foreign('position_id')->references('id')->on('employeetype');
            $table->unsignedBigInteger('departament_id');
            $table->foreign('departament_id')->references('id')->on('departments');
            $table->integer('vacations_days_per_year',11);
            $table->integer('probation_period_months',11);
            $table->tinyInteger('is_active',1);
            $table->string('termination_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato');
    }
};
