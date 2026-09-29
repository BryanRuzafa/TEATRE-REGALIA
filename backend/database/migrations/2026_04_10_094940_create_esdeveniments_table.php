<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('esdeveniments', function (Blueprint $table) {
            $table->id();
            $table->string('titol');
            $table->text('descripcio')->nullable();
            $table->dateTime('data_hora');
            $table->string('recinte');
            $table->string('imatge_url')->nullable();
            $table->decimal('preu_base', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('esdeveniments');
    }
};
