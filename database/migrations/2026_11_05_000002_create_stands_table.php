<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->string('categoria', 40)->default('Gastronomia');
            $table->string('numero', 20)->nullable();
            $table->unsignedTinyInteger('ilha')->nullable();
            $table->unsignedTinyInteger('posicao')->nullable();
            $table->string('lado', 1)->nullable();
            $table->string('referencia')->nullable();
            // Retângulo do lote no mapa (coordenadas do SVG)
            $table->unsignedSmallInteger('rect_x')->default(0);
            $table->unsignedSmallInteger('rect_y')->default(0);
            $table->unsignedSmallInteger('rect_w')->default(0);
            $table->unsignedSmallInteger('rect_h')->default(0);
            // Perfil público
            $table->text('sobre')->nullable();
            $table->string('horario')->nullable();
            $table->string('cardapio_url', 500)->nullable();
            $table->string('cardapio_path')->nullable();
            $table->string('instagram')->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('stand_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('ordem')->default(0);
            $table->timestamps();
        });

        Schema::create('stand_user', function (Blueprint $table) {
            $table->foreignId('stand_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['stand_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stand_user');
        Schema::dropIfExists('stand_fotos');
        Schema::dropIfExists('stands');
    }
};
