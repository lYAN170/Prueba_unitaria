<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personaas', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 80);
            $table->string('apellido_paterno', 80);
            $table->string('apellido_materno', 80);
            $table->enum('sexo', ['M', 'F']);
            $table->string('contacto', 80);
            $table->string('direccion', 80);
            $table->string('telefono', 15); 
            $table->string('email', 80)->unique();
            $table->string('tipo_persona', 20);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('documento_id')->unique()->constrained('documentos')->onDelete('cascade');
            $table->string('documento_entidad', 80);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personaas');
    }
};
