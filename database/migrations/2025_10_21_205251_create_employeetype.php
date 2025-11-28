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
        Schema::create('employeetype', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 10)->nullable();
            $table->text('description')->nullable();
            $table->integer('level')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('protected')->default(false);
            $table->string('color', 7)->default('#007bff');
            $table->string('icon', 50)->default('fas fa-user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employeetype');
    }
};
