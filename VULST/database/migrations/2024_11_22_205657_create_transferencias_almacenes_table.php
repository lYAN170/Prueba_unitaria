<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transferencias_almacenes', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('producto_id'); 
            $table->unsignedBigInteger('almacen_origen_id'); 
            $table->unsignedBigInteger('almacen_destino_id'); 
            $table->integer('cantidad')->default(0); 
            $table->timestamp('fecha_transferencia')->default(DB::raw('CURRENT_TIMESTAMP')); 
            $table->timestamps(); 

            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade'); 
            $table->foreign('almacen_origen_id')->references('id')->on('almacenes')->onDelete('cascade'); 
            $table->foreign('almacen_destino_id')->references('id')->on('almacenes')->onDelete('cascade'); 

            $table->index(['producto_id', 'almacen_origen_id', 'almacen_destino_id'], 'transferencias_producto_almacen_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencias_almacenes');
    }
};
