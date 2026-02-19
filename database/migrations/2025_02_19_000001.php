<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('first_name', 80);
            $table->string('last_name', 80);
            $table->string('dni', 15)->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable()->unique();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['masculino', 'femenino', 'otro', 'no_especifica'])->nullable();
            $table->string('photo')->nullable();

            // Ubicación (via API Regiones Perú)
            $table->string('department', 80)->nullable();
            $table->string('province', 80)->nullable();
            $table->string('district', 80)->nullable();
            $table->string('address', 200)->nullable();

            // Marketing & captación
            $table->enum('acquisition_source', [
                'instagram', 'facebook', 'tiktok', 'google',
                'referido', 'walk_in', 'whatsapp', 'otro'
            ])->nullable()->comment('Cómo llegó al salón');
            $table->string('referred_by')->nullable()->comment('Nombre o ID de quien refirió');
            $table->enum('hair_type', ['liso', 'ondulado', 'rizado', 'muy_rizado', 'otro'])->nullable();
            $table->json('services_interest')->nullable()->comment('Servicios de interés o que consume');
            $table->text('notes')->nullable()->comment('Notas internas de marketing/atención');
            $table->string('tags')->nullable()->comment('Etiquetas libres separadas por coma');

            // Segmentación automática
            $table->unsignedInteger('visit_count')->default(0);
            $table->date('first_visit_at')->nullable();
            $table->date('last_visit_at')->nullable();
            $table->enum('client_type', ['nuevo', 'recurrente', 'vip', 'inactivo', 'unico'])
                ->default('nuevo')
                ->comment('nuevo=sin visitas, unico=solo 1 visita hace +90d, recurrente=2+, vip=5+, inactivo=sin visita en 60d');

            // Inscripción a curso al crear
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['client_type', 'last_visit_at']);
            $table->index('acquisition_source');
            $table->index('district');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};