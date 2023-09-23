<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre', 255);
            $table->string('descripcion', 255)->nullable();
            $table->decimal('precio', 24, 2);
            $table->string('medida');
            $table->double('stock_minimo');
            $table->double('stock_actual');
            $table->string('estado');
            $table->string('foto', 255)->nullable();
            $table->date('fecha_registro');
            $table->integer('status');
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
        Schema::dropIfExists('productos');
    }
}
