<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 800);
            $table->bigInteger('id_unimedida')->unsigned();
            $table->bigInteger('id_objespecifico')->unsigned();
            $table->decimal('costo', 10, 2);
            $table->integer('visible');

            $table->foreign('id_objespecifico')->references('id')->on('obj_especifico');
            $table->foreign('id_unimedida')->references('id')->on('unidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material');
    }
}
