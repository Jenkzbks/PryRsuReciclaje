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
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('dni',10);
            $table->string('lastnames',200);
            $table->string('names',100);
            $table->date('birthay');
            $table->string('license',20);
            $table->string('address',200);
            $table->string('email',100);
            $table->string('photo',100);
            $table->string('phone',20);
            $table->tinyInteger('status',1);
            $table->string('password',255);
             $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('employeetype');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
