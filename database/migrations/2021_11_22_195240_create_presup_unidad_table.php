<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupUnidadTable extends Migration
{
    /**
     * Registro para materiales base
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presup_unidad', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_anio')->unsigned();
            $table->bigInteger('id_departamento')->unsigned();
            $table->bigInteger('id_estado')->unsigned();

            $table->foreign('id_anio')->references('id')->on('anio');
            $table->foreign('id_departamento')->references('id')->on('departamento');
            $table->foreign('id_estado')->references('id')->on('estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presup_unidad');
    }
}
