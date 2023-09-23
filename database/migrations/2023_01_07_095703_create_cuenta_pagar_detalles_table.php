<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaPagarDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_pagar_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("cuenta_pagar_id");
            $table->decimal("monto", 24, 2);
            $table->decimal("saldo", 24, 2);
            $table->decimal("total", 24, 2);
            $table->string("descripcion", 255)->nullable();
            $table->date("fecha");
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
        Schema::dropIfExists('cuenta_pagar_detalles');
    }
}
