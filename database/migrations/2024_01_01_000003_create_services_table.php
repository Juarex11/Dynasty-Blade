<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');                         // Nombre del servicio
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();  // Descripción corta
            $table->text('description')->nullable();        // Descripción larga/detallada
            $table->string('cover_image')->nullable();      // Imagen principal

            // Precio y duración
            $table->decimal('price', 10, 2);                // Precio base
            $table->decimal('price_max', 10, 2)->nullable();// Precio máximo (rango)
            $table->integer('duration_minutes');             // Duración en minutos
            $table->integer('buffer_minutes')->default(0);  // Tiempo de preparación/limpieza

            // Configuración de citas
            $table->boolean('requires_deposit')->default(false); // Requiere seña
            $table->decimal('deposit_amount', 10, 2)->nullable();// Monto de seña
            $table->boolean('online_booking')->default(true);    // Permite reserva online

            // Estado
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Destacado
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};