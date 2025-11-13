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
        Schema::create('source_texts', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('title')->nullable();
            $table->string('author')->nullable();
            $table->string('language')->default('en');
            $table->integer('word_count');
            $table->integer('character_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_texts');
    }
};
