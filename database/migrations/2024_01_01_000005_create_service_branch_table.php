<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Un servicio puede ofrecerse en una o más sedes
        Schema::create('service_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            // Precio diferenciado por sede (opcional, si es null usa el precio base)
            $table->decimal('price_override', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->unique(['service_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_branch');
    }
};