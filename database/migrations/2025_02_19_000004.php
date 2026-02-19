<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Estructura de pagos definida para la apertura
        Schema::table('course_openings', function (Blueprint $table) {
            // Tipo de cobro: 'unico' | 'mensual' | 'por_sesion' | 'semanal' | 'personalizado'
            $table->string('payment_type', 30)->default('unico')->after('price_promo');
            // Para mensual: cuántas cuotas
            $table->unsignedTinyInteger('payment_installments')->nullable()->after('payment_type');
            // Precio por unidad (sesión, semana, mes) según payment_type
            $table->decimal('payment_unit_price', 10, 2)->nullable()->after('payment_installments');
        });

        // Cuotas / pagos individuales de cada estudiante
        Schema::create('course_student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_opening_student_id')
                  ->constrained('course_opening_student')
                  ->cascadeOnDelete();
            $table->foreignId('course_opening_id')->constrained()->cascadeOnDelete();

            // Tipo de cuota: 'unico' | 'cuota_N' | 'sesion_N' | 'semana_N' | 'mes_N' | 'ajuste'
            $table->string('payment_type', 30)->default('unico');
            $table->unsignedSmallInteger('installment_number')->nullable(); // Nro de cuota/sesión/semana/mes
            $table->string('concept', 200)->nullable();                      // Descripción libre

            $table->decimal('amount_due',  10, 2)->default(0);  // Monto esperado
            $table->decimal('amount_paid', 10, 2)->default(0);  // Monto pagado

            // 'pendiente' | 'pagado' | 'parcial' | 'vencido' | 'becado' | 'anulado'
            $table->string('status', 20)->default('pendiente');

            $table->date('due_date')->nullable();      // Fecha límite de pago
            $table->date('paid_at')->nullable();       // Fecha en que se pagó

            // Método de pago: 'efectivo' | 'transferencia' | 'tarjeta' | 'yape' | 'plin' | 'otro'
            $table->string('payment_method', 30)->nullable();
            $table->string('reference', 100)->nullable();  // Nro de operación / voucher
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['course_opening_id', 'status']);
            $table->index(['course_opening_student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_student_payments');
        Schema::table('course_openings', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'payment_installments', 'payment_unit_price']);
        });
    }
};