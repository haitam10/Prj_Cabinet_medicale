<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained('users')->onDelete('cascade');
            $table->date('date_consultation');
            $table->string('motif')->nullable();
            $table->text('symptomes')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('traitement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('consultations');
    }
};
