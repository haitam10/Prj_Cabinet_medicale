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
    Schema::create('ordon_doc', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_docteur');
        $table->string('logo_file_path')->nullable();
        $table->string('file_path')->nullable();
        $table->string('nom_cabinet')->nullable();
        $table->string('addr_cabinet')->nullable();   // 🆕 Address
        $table->string('tel_cabinet')->nullable();    // 🆕 Phone number
        $table->text('desc_cabinet')->nullable();
        $table->text('descOrdonn')->nullable();
        $table->boolean('isSelected')->default(false);
        $table->timestamps();

        $table->foreign('id_docteur')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordon_doc');
    }
};
