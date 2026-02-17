<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un empleado puede trabajar en una o más sedes
        Schema::create('employee_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_primary')->default(false); // Sede principal del empleado
            $table->timestamps();
            $table->unique(['employee_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_branch');
    }
};