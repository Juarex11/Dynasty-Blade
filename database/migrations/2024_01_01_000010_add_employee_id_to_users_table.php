<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Rol del usuario en el sistema
            $table->enum('role', [
                'admin',        // Administrador total
                'manager',      // Manager de sede
                'employee',     // Empleado con acceso
                'receptionist', // Recepcionista
                'client',       // Cliente con acceso
            ])->default('client')->after('email');

            // Vincula el usuario a un empleado (si aplica)
            // Se agrega después de que exista la tabla employees
            // NOTA: Añadir la FK después con otro migration si employees se crea después de users
            $table->unsignedBigInteger('employee_id')->nullable()->after('role');
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            // Avatar del usuario
            $table->string('avatar')->nullable()->after('employee_id');

            // Estado de la cuenta
            $table->boolean('is_active')->default(true)->after('avatar');

            // Última actividad
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Teléfono
            $table->string('phone')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn([
                'role',
                'employee_id',
                'avatar',
                'is_active',
                'last_login_at',
                'phone',
            ]);
        });
    }
};