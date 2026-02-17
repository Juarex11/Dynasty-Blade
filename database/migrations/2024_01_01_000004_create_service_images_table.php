<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('path');                         // Ruta del archivo
            $table->string('alt')->nullable();              // Texto alternativo
            $table->string('type')->default('gallery');     // gallery | before_after | cover
            $table->boolean('is_primary')->default(false);  // Imagen principal
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
    }
};