<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupUnidadDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presup_unidad_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_material')->unsigned();
            $table->decimal('cantidad', 10, 2);
            $table->integer('periodo');
            $table->decimal('precio', 10, 2);

            $table->foreign('id_presup_unidad')->references('id')->on('presup_unidad');
            $table->foreign('id_material')->references('id')->on('material');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presup_unidad_detalle');
    }
}
