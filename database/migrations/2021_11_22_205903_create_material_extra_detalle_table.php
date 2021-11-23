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
            $table->bigInteger('id_material_extra')->unsigned();

            $table->string('descripcion', 800);
            $table->decimal('costo', 10, 2);
            $table->integer('cantidad');
            $table->integer('periodo');

            $table->foreign('id_material_extra')->references('id')->on('material');
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
