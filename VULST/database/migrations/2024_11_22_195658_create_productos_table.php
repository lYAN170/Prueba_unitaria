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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name')->nullable();
            $table->string('Descripcion'); 
            $table->string('image')->nullable();  
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete(); 
            $table->string('slug')->unique(); 
            $table->longText('detalles_adicionales')->nullable(); 
            $table->decimal('precio_venta', 10, 2)->default(0); 
            $table->decimal('precio_compra', 10, 2)->default(0); 
            $table->unsignedInteger('stock_minimo')->default(5);
            $table->integer('estado')->default(1);
            $table->timestamp('fecha_creacion')->nullable();  
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};


