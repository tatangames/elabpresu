<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialExtraDetalleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_extra_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_presup_unidad')->unsigned();
            $table->bigInteger('id_unidad')->unsigned();

            $table->string('descripcion', 800);
            $table->decimal('costo', 10, 2);
            $table->decimal('cantidad', 10, 2);
            $table->integer('periodo');

            $table->foreign('id_presup_unidad')->references('id')->on('presup_unidad');
            $table->foreign('id_unidad')->references('id')->on('unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_extra_detalle');
    }
}
