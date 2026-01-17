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
        Schema::create('cabinets', function (Blueprint $table) {
            $table->id();  // This will automatically create an unsignedBigInteger column
            $table->string('id_docteur')->nullable();
            $table->string('nom_cabinet');
            $table->string('addr_cabinet')->nullable();
            $table->string('tel_cabinet')->nullable();
            $table->text('descr_cabinet')->nullable();
            $table->timestamps();

            // Ensure the table is using the InnoDB engine for foreign key support
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabinets');
    }
};
