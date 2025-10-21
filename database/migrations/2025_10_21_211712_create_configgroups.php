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
        Schema::create('configgroups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emplooyeegroup_id');
            $table->foreign('emplooyeegroup_id')->references('id')->on('employeegroups'); 
            $table->unsignedBigInteger('emplooyee_id');
            $table->foreign('emplooyee_id')->references('id')->on('employee'); 
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configgroups');
    }
};
