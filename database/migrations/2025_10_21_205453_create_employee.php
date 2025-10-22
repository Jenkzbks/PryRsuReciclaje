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
            $table->string('dni', 10)->unique();
            $table->string('names', 100);
            $table->string('lastnames', 200);
            $table->date('birthday');
            $table->string('license', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('password', 255)->nullable();
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
