<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');                   // Ej: Cabello, Uñas, Faciales
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();       // Ícono o emoji
            $table->string('color')->nullable();      // Color de la categoría (#hex)
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};