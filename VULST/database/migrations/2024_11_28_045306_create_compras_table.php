<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Asegúrate de importar DB

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            // Establecer valor predeterminado para fecha_hora
            $table->dateTime('fecha_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('impuesto', 8, 2)->unsigned();
            $table->string('numero_comprobante', 255);
            $table->decimal('total', 8, 2)->unsigned();
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->onDelete('set null');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->onDelete('set null'); // Corregido el nombre a 'proveedor_id'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
};
