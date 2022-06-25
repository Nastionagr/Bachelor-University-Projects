<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand');
            $table->unsignedInteger('weight');
            $table->double('price',6,2);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('sold')->default(0);
            $table->json('images')->default('["/img/default_product.png"]');
            $table->json('ingredients')->nullable();
            $table->string('description');
            $table->string('origin_country')->nullable();
            $table->string('dimensions')->nullable();
            $table->unsignedInteger('nutrition_id')->nullable();
            $table->unsignedInteger('discount')->default(0);
            $table->double('price_discounted',6,2)->nullable();
            $table->boolean('is_offered')->default(true);
            $table->foreign('nutrition_id')->references('id')->on('nutrition');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *`
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}