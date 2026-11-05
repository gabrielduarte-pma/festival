<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->string('chave')->primary();
            $table->longText('valor')->nullable();
            $table->timestamps();
        });

        Schema::create('alteracoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('stand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('resumo');
            $table->json('dados')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alteracoes');
        Schema::dropIfExists('configuracoes');
    }
};
