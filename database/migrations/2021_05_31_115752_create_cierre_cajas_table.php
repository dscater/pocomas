<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierreCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierre_cajas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('caja_id')->unsigned();
            $table->decimal('monto_total', 24, 2);
            $table->date('fecha_cierre');
            $table->string('descripcion', 255)->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->date('fecha_registro');
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('caja_id')->references('id')->on('cajas')->ondelete('no action')->onupdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->ondelete('no action')->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cierre_cajas');
    }
}
