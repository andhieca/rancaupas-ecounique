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
        Schema::table('tourisms', function (Blueprint $table) {
            $table->decimal('distance_km', 5, 1)->default(0)->after('price_wna');
            $table->string('status')->default('aktif')->after('score_keseruan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tourisms', function (Blueprint $table) {
            $table->dropColumn(['distance_km', 'status']);
        });
    }
};
