<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Horarios semanales de cada empleado por sede
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            // Día de la semana: 0=Domingo, 1=Lunes ... 6=Sábado
            $table->tinyInteger('day_of_week'); // 0-6

            $table->time('start_time');         // Hora de inicio
            $table->time('end_time');           // Hora de fin

            // Horario de descanso (break)
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            $table->boolean('is_working')->default(true); // false = día libre

            $table->timestamps();

            // Un empleado no puede tener dos registros para el mismo día en la misma sede
            $table->unique(['employee_id', 'branch_id', 'day_of_week']);
        });

        // Excepciones al horario: vacaciones, días libres especiales, horas extra
        Schema::create('employee_schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('date');                           // Fecha específica de la excepción
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('type', [
                'day_off',      // Día libre
                'vacation',     // Vacaciones
                'sick_leave',   // Baja por enfermedad
                'special_hours',// Horario especial ese día
            ])->default('day_off');
            $table->string('reason')->nullable();           // Motivo
            $table->boolean('is_approved')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_schedule_exceptions');
        Schema::dropIfExists('employee_schedules');
    }
};