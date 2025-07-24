<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinets', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();  // unsignedBigInteger primary key
            $table->unsignedBigInteger('id_docteur')->nullable();  // FK vers users.id
            $table->string('nom_cabinet');
            $table->string('addr_cabinet')->nullable();
            $table->string('tel_cabinet')->nullable();
            $table->text('descr_cabinet')->nullable();
            $table->timestamps();

            // Foreign key optionnelle vers users
            $table->foreign('id_docteur')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cabinets', function (Blueprint $table) {
            // Supprime la contrainte étrangère avant de supprimer la table
            $table->dropForeign(['id_docteur']);
        });
        Schema::dropIfExists('cabinets');
    }
};
