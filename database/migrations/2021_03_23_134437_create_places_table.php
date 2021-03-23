<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->float('rating');
            //location
            $table->string('address_address')->nullable();
            $table->double('address_latitude')->nullable();
            $table->double('address_longitude')->nullable();
            //
            $table->string('phone');
            $table->string('work_start');
            $table->string('work_end');
            $table->string('description');
            $table->unsignedBigInteger('review_id');
            $table->foreign('review_id')->references('id')->on('reviews');
            $table->string('capacity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
}
