<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Credenciales de acceso (para uso futuro)
            $table->string('username')->nullable()->unique()->after('email');
            $table->string('password')->nullable()->after('username');

            // Modo de cliente: frecuente u ocasional
            // Para clientes ocasionales no es necesario registrar tantos datos
            $table->enum('client_mode', ['frecuente', 'ocasional'])->default('frecuente')->after('client_type');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'client_mode']);
        });
    }
};