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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 100);
            $table->string('plate', 20);
            $table->integer('year')->nullable();
            $table->double('load_capacity')->nullable();
            $table->text('description')->nullable();
            $table->integer('status');
            $table->integer('passengers')->nullable();
            $table->double('fuel_capacity')->nullable();
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unsignedBigInteger('model_id');
            $table->foreign('model_id')->references('id')->on('brandmodels');
            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('vehicletypes');
            $table->unsignedBigInteger('color_id');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
