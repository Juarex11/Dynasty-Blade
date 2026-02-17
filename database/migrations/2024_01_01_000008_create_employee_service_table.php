<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Especialidades: qué servicios puede realizar cada empleado
        Schema::create('employee_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            // El empleado puede tener precio propio para este servicio (opcional)
            $table->decimal('price_override', 10, 2)->nullable();

            // Nivel de especialización
            $table->enum('skill_level', ['junior', 'mid', 'senior', 'expert'])
                ->default('mid');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['employee_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_service');
    }
};