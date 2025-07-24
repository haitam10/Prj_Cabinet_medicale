<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doc_model', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('id_docteur'); // FK vers users.id
            $table->unsignedBigInteger('id_cabinet'); // FK vers cabinets.id

            $table->string('model_nom')->nullable();
            $table->string('logo_file_path')->nullable();
            $table->text('descr_head')->nullable();
            $table->text('descr_body')->nullable();
            $table->text('descr_footer')->nullable();
            $table->enum('document', ['certificat', 'ordonnance']);
            $table->boolean('is_selected')->default(false);
            $table->timestamps();

            // Clés étrangères
            $table->foreign('id_docteur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_cabinet')->references('id')->on('cabinets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doc_model');
    }
};
