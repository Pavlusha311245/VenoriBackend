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
            $table->string('image_url');
            $table->string('type');
            $table->float('rating')->default(0);
            $table->unsignedInteger('reviewsCount')->default(0);
            $table->string('address_full');
            $table->double('address_lat');
            $table->double('address_lon');
            $table->string('phone')->nullable();
            $table->string('description');
            $table->unsignedInteger('capacity');
            $table->decimal('table_price');
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
