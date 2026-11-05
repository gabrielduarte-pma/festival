<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('expositor')->after('password');
            $table->boolean('ativo')->default(true)->after('role');
            $table->string('telefone', 30)->nullable()->after('ativo');
            $table->string('invite_token', 64)->nullable()->unique()->after('telefone');
            $table->timestamp('invite_expires_at')->nullable()->after('invite_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'ativo', 'telefone', 'invite_token', 'invite_expires_at']);
        });
    }
};
