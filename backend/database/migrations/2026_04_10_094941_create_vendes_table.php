<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vendes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esdeveniment_id')->constrained('esdeveniments')->onDelete('cascade');
            $table->foreignId('seient_id')->constrained('seients')->onDelete('cascade');
            $table->string('nom_comprador');
            $table->string('email_comprador');
            $table->decimal('preu_final', 10, 2);
            $table->string('codi_confirmacio');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vendes');
    }
};
