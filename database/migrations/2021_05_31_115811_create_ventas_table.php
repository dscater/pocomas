<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('caja_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('cliente_id')->unsigned();
            $table->double('cantidad_total');
            $table->decimal('monto_total', 24, 2);
            $table->string('tipo_venta');
            $table->date('fecha_venta');
            $table->time('hora_venta');
            $table->date('fecha_registro');
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('caja_id')->references('id')->on('cajas')->ondelete('no action')->onupdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->ondelete('no action')->onupdate('cascade');
            $table->foreign('cliente_id')->references('id')->on('clientes')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
