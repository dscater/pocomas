<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaPagarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_pagars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ingreso_producto_id');
            $table->unsignedBigInteger('proveedor_id');
            $table->decimal('monto_total', 24, 2);
            $table->decimal('saldo', 24, 2);
            $table->text('descripcion');
            $table->date('fecha_registro');
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
        Schema::dropIfExists('cuenta_pagars');
    }
}
