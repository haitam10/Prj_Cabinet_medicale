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
        Schema::create('rendezvous', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('secretaire_id')->nullable();
            $table->dateTime('date');
            $table->string('statut')->default('en attente');
            $table->text('motif');

            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('secretaire_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendezvous');
    }
};
