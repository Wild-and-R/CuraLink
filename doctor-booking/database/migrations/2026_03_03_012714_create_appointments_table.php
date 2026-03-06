<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
        $table->string('patient_name');
        $table->string('phone');
        $table->text('complaint')->nullable();
        $table->date('appointment_date');
        $table->time('appointment_time');
        $table->string('status')->default('booked');
        $table->timestamps();

        $table->unique(['doctor_id','appointment_date','appointment_time']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
