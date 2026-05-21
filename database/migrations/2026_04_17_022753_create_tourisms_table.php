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
        Schema::create('tourisms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price_wni')->nullable();
            $table->integer('price_wna')->nullable();
            $table->text('facilities_list')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('map_url', 1000)->nullable();
            
            // SAW Scores (1 to 5)
            $table->integer('score_anggaran')->default(1);
            $table->integer('score_fasilitas')->default(1);
            $table->integer('score_jarak')->default(1);
            $table->integer('score_keseruan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tourisms');
    }
};
