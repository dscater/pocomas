<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaCentralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja_centrals', function (Blueprint $table) {
            $table->id();
            $table->date("fecha");
            $table->decimal("monto", 24, 2);
            $table->text("descripcion");
            $table->unsignedBigInteger("concepto_id");
            $table->unsignedBigInteger("cuenta_pagar_id");
            $table->string("tipo", 255);
            $table->string("sw_egreso", 155)->nullable();
            $table->date("fecha_registro", 255);
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
        Schema::dropIfExists('caja_centrals');
    }
}
