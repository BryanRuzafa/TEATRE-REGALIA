<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('seients', function (Blueprint $table) {
            $table->foreignId('esdeveniment_id')->after('id')->nullable()->constrained('esdeveniments')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::table('seients', function (Blueprint $table) {
            $table->dropForeign(['esdeveniment_id']);
            $table->dropColumn('esdeveniment_id');
        });
    }
};
