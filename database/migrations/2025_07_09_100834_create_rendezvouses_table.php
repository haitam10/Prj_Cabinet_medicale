<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendezvous', function (Blueprint $table) {
            $table->bigIncrements('id');  // id auto-increment entier

            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('secretaire_id')->nullable();

            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->integer('duration')->default(30);

            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->enum('appointment_type', ['consultation', 'follow_up', 'emergency', 'routine'])->default('consultation');

            $table->text('reason')->nullable();
            $table->text('patient_notes')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('feedback')->nullable();

            $table->timestamps();

            // Index
            $table->index('patient_id');
            $table->index('medecin_id');
            $table->index('secretaire_id');

            // Foreign keys
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('secretaire_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendezvous');
    }
};
