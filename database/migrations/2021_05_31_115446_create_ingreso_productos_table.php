<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_productos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('producto_id')->unsigned();
            $table->double('cantidad');
            $table->decimal('precio_compra', 24, 2);
            $table->string('descripcion', 255)->nullable();
            $table->decimal('saldo', 24, 2);
            $table->date('fecha_ingreso');
            $table->date('fecha_registro');
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('producto_id')->references('id')->on('productos')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingreso_productos');
    }
}
