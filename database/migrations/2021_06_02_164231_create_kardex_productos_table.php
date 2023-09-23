<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKardexProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kardex_productos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('producto_id')->unsigned();
            $table->date('fecha');
            $table->string('detalle');
            $table->string('tipo');
            $table->double('ingreso_c')->nullable();
            $table->double('salida_c')->nullable();
            $table->double('saldo_c')->nullable();
            $table->decimal('cu', 24, 2)->nullable();
            $table->decimal('ingreso_m', 24, 2)->nullable();
            $table->decimal('salida_m', 24, 2)->nullable();
            $table->decimal('saldo_m', 24, 2)->nullable();
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
        Schema::dropIfExists('kardex_productos');
    }
}
