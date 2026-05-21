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
    Schema::create('patients', function (Blueprint $table) {
        $table->id();
        //RELASI DENGAN USER
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');
        //DATA PASIEN
        $table->string('name');
        $table->string('email')->unique();
        $table->date('date_of_birth');
        $table->text('address');
        $table->string('phone');
        $table->string('photo')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
