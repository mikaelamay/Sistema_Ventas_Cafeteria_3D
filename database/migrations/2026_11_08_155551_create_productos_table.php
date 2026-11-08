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
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('categoria')->nullable();
            $table->string('imagen_path')->nullable();
            $table->string('modelo_3d_path')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'categoria']);
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
