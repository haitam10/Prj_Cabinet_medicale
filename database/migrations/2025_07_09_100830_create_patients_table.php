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
            $table->string('cin')->unique();
            $table->string('nom');
            $table->string('sexe');
            $table->date('date_naissance');
            $table->string('contact');
            $table->string('adresse')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone_secondaire')->nullable();
            $table->string('groupe_sanguin')->nullable();
            $table->text('allergies')->nullable();
            $table->text('antecedents')->nullable();
            $table->text('medicaments')->nullable();
            $table->float('poids')->nullable();
            $table->float('taille')->nullable();
            $table->string('profession')->nullable();
            $table->string('situation_familiale')->nullable();
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
