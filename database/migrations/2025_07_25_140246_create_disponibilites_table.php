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
        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medecin_id');
            $table->unsignedBigInteger('secretaire_id')->nullable();
            $table->date('date');
            $table->time('heure_entree');
            $table->time('heure_sortie');
            $table->timestamps();

            $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('secretaire_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilites');
    }
};
