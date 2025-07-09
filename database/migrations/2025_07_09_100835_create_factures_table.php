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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('medecin_id')->nullable();
            $table->unsignedBigInteger('secretaire_id')->nullable();
            $table->decimal('montant', 10, 2);
            $table->string('statut');
            $table->date('date');
            $table->unsignedBigInteger('utilisateur_id')->nullable();

            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('secretaire_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('utilisateur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
