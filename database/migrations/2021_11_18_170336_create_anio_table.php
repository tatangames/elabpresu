<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnioTable extends Migration
{
    /**
     * año de presupuesto.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anio');
    }
}
