<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Nombre del local
            $table->string('slug')->unique();              // Para URLs amigables
            $table->string('address');                     // Dirección
            $table->string('district')->nullable();        // Distrito/Barrio
            $table->string('city');                        // Ciudad
            $table->string('phone')->nullable();           // Teléfono del local
            $table->string('email')->nullable();           // Email del local
            $table->string('whatsapp')->nullable();        // WhatsApp del local
            $table->text('description')->nullable();       // Descripción
            $table->string('image')->nullable();           // Foto del local
            $table->decimal('latitude', 10, 8)->nullable(); // Coordenada lat
            $table->decimal('longitude', 11, 8)->nullable();// Coordenada lng
            $table->json('opening_hours')->nullable();     // Horario de atención JSON
            $table->boolean('is_active')->default(true);   // Activo/Inactivo
            $table->integer('sort_order')->default(0);     // Orden de visualización
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};