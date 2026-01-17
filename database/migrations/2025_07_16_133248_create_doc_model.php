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
        Schema::create('doc_model', function (Blueprint $table) {
            $table->id();  // Automatically creates an unsignedBigInteger primary key
            $table->unsignedBigInteger('id_docteur');  // Foreign key column to `users` table
            $table->unsignedBigInteger('id_cabinet');  // Foreign key column to `cabinets` table
             $table->string('model_nom')->nullable();
            $table->string('logo_file_path')->nullable();
            $table->text('descr_head')->nullable();
            $table->text('descr_body')->nullable();
            $table->text('descr_footer')->nullable();
            $table->enum('document', ['certificat', 'ordonnance']);
            $table->boolean('is_selected')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_docteur')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_cabinet')->references('id')->on('cabinets')->onDelete('cascade');

            // Ensure the table uses the InnoDB engine for foreign key support
            $table->engine = 'InnoDB';
        });
                            }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_model');
    }
};
