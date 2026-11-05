<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dias', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 10)->unique();
            $table->string('sigla', 5);
            $table->string('nome', 30);
            $table->string('data', 10);
            $table->unsignedTinyInteger('ordem')->default(0);
            $table->timestamps();
        });

        Schema::create('atracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dia_id')->constrained('dias')->cascadeOnDelete();
            $table->string('nome');
            $table->string('hora', 20)->nullable();
            $table->string('palco')->nullable();
            $table->string('img')->nullable();
            $table->boolean('destaque')->default(false);
            $table->boolean('flip')->default(false);
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atracoes');
        Schema::dropIfExists('dias');
    }
};
