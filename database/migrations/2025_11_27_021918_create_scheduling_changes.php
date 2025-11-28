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
        Schema::create('scheduling_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scheduling_id');
            $table->foreign('scheduling_id')->references('id')->on('schedulings')->onDelete('cascade');
            $table->unsignedBigInteger('reason_id');    
            $table->foreign('reason_id')->references('id')->on('reasons');
            $table->string('change_type');               
            $table->text('old_value')->nullable();      
            $table->text('new_value')->nullable();    
            $table->text('notes')->nullable();   
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduling_changes');
    }
};
