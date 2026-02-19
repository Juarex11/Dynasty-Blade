<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_openings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 30)->nullable();
            $table->string('name', 200)->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->json('days_of_week')->nullable()->comment('[1,3,5] = lun,mie,vie');
            $table->unsignedSmallInteger('total_sessions')->default(1);
            $table->unsignedSmallInteger('max_students')->nullable();
            $table->unsignedSmallInteger('enrolled_count')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_promo', 10, 2)->nullable();
            $table->date('promo_until')->nullable();
            $table->string('promo_label', 80)->nullable();
            $table->enum('status', ['borrador', 'publicado', 'en_curso', 'finalizado', 'cancelado'])->default('borrador');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['course_id', 'status']);
            $table->index(['start_date', 'status']);
        });

        Schema::create('course_opening_instructor', function (Blueprint $table) {
            $table->foreignId('course_opening_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->primary(['course_opening_id', 'employee_id']);
        });

        Schema::create('course_opening_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_opening_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price_paid', 10, 2)->nullable();
            $table->enum('payment_status', ['pendiente', 'pagado', 'parcial', 'becado'])->default('pendiente');
            $table->date('enrolled_at')->nullable();
            $table->enum('status', ['inscrito', 'en_curso', 'completado', 'abandonado', 'retirado'])->default('inscrito');
            $table->boolean('certificate_issued')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_opening_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('session_number');
            $table->date('date');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->string('topic', 200)->nullable();
            $table->enum('status', ['programada', 'realizada', 'cancelada', 'postergada'])->default('programada');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['course_opening_id', 'date']);
        });

        Schema::create('course_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_opening_student_id')->constrained('course_opening_student')->cascadeOnDelete();
            $table->enum('status', ['presente', 'tardanza', 'ausente', 'justificado'])->default('presente');
            $table->text('observation')->nullable();
            $table->timestamps();
          $table->unique(
    ['course_session_id', 'course_opening_student_id'],
    'course_attendance_unique'
);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_attendances');
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('course_opening_student');
        Schema::dropIfExists('course_opening_instructor');
        Schema::dropIfExists('course_openings');
    }
};