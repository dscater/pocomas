<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresoCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingreso_cajas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('caja_id')->unsigned();
            $table->string('tipo');
            $table->bigInteger('registro_id')->unsigned();
            $table->decimal('monto_total');
            $table->unsignedBigInteger("concepto_id");
            $table->date('fecha');
            $table->time('hora');
            $table->string("sw_egreso")->nullable();
            $table->integer("estado");
            $table->timestamps();

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
        Schema::dropIfExists('ingreso_cajas');
    }
}
