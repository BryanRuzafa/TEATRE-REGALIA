<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('seients', function (Blueprint $table) {
            $table->id();
            $table->string('id_referencia');
            $table->string('estat')->default('disponible'); // disponible, reservat, venut
            $table->string('reservat_per')->nullable();
            $table->timestamp('temps_bloqueig_finalitza')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('seients');
    }
};
