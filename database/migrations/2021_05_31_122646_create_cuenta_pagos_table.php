<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_pagos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cuenta_id')->unsigned();
            $table->bigInteger('caja_id')->unsigned();
            $table->decimal('monto', 24, 2);
            $table->string('observacion', 255)->nullable();
            $table->date('fecha_pago');
            $table->timestamps();

            $table->foreign('cuenta_id')->references('id')->on('cuenta_cobrars')->ondelete('no action')->onupdate('cascade');
            $table->foreign('caja_id')->references('id')->on('cajas')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuenta_pagos');
    }
}
