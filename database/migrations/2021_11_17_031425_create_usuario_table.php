<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioTable extends Migration
{
    /**
     * Usuarios.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_departamento')->unsigned();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->boolean('activo');
            $table->string('usuario', 50);
            $table->string('password', 255);

            $table->foreign('id_departamento')->references('id')->on('departamento');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario');
    }
}
