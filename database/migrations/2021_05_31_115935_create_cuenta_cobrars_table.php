<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentaCobrarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuenta_cobrars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('venta_id')->unsigned();
            $table->bigInteger('cliente_id')->unsigned();
            $table->decimal('monto_deuda', 24, 2);
            $table->decimal('saldo', 24, 2);
            $table->string('estado');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas')->ondelete('no action')->onupdate('cascade');
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
        Schema::dropIfExists('cuenta_cobrars');
    }
}
