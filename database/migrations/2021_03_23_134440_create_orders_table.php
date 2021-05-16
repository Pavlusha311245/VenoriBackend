<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['In Progress', 'Rejected', 'Confirmed']);
            $table->decimal('price');
            $table->date('date');
            $table->unsignedInteger('people');
            $table->unsignedInteger('staying');
            $table->time('time');
            $table->time('staying_end')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('place_id');
            $table->foreign('place_id')->references('id')->on('places')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('orders');
    }
}
