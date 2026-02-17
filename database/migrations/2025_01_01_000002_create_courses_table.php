<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_category_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('short_description', 300)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('price_max', 10, 2)->nullable();
            $table->decimal('duration_hours', 6, 2)->default(1); // 1.5 = 1h30m
            $table->enum('modality', ['presencial', 'online', 'mixto'])->default('presencial');
            $table->enum('level', ['basico', 'intermedio', 'avanzado'])->default('basico');
            $table->string('instructor', 150)->nullable();
            $table->unsignedSmallInteger('max_students')->nullable();
            $table->boolean('has_certificate')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};