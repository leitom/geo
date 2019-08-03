<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoModelsTable extends Migration
{
    public function up()
    {
        Schema::create('geo_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->decimal('longitude', 11, 8);
            $table->decimal('latitude', 10, 8);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('geo_models');
    }
}
