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
            $table->double('shipping_price',8,2);
            $table->double('payment_price',8,2);
            $table->double('total',8,2);
            $table->boolean('paid')->default(false);
            $table->boolean('shipped')->default(false);
            $table->boolean('delivered')->default(false);
            $table->string('shipping_method');
            $table->string('payment_method');
            $table->unsignedInteger('gift_id')->nullable();
            $table->foreign('gift_id')->references('id')->on('gifts');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email');
            $table->string('phone_number');
            $table->string('street');
            $table->string('apartment');
            $table->string('city');
            $table->string('zip_code');
            $table->string('country');
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