<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->nullable()->unique();        // Documento de identidad
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();

            // Datos laborales
            $table->string('position');                         // Cargo: Estilista, Colorista, etc.
            $table->text('bio')->nullable();                    // Descripción/Presentación
            $table->date('hire_date');                          // Fecha de ingreso
            $table->date('end_date')->nullable();               // Fecha de baja (si aplica)
            $table->decimal('commission_rate', 5, 2)->nullable(); // % comisión sobre servicios
            $table->enum('employment_type', [
                'full_time',    // Tiempo completo
                'part_time',    // Medio tiempo
                'freelance',    // Freelance/Por servicio
            ])->default('full_time');

            // Redes sociales (para mostrar en perfil público)
            $table->string('instagram')->nullable();
            $table->string('tiktok')->nullable();

            // Acceso al sistema
            $table->boolean('has_system_access')->default(false); // ¿Tiene acceso al sistema?
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();  // Vinculado a un usuario del sistema

            // Estado
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);          // Orden en listados

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};