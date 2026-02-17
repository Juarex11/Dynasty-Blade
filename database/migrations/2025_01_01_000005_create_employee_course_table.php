<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            // Rol del empleado en el curso
            $table->enum('role', ['estudiante', 'instructor'])->default('estudiante');
            // Estado si es estudiante
            $table->enum('status', ['inscrito', 'en_curso', 'completado', 'abandonado'])->nullable();
            $table->date('enrolled_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->string('certificate_number', 100)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_course');
    }
};