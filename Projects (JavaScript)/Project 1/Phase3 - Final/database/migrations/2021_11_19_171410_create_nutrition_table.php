<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNutritionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nutrition', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('energy_kj');
            $table->unsignedInteger('energy_kcal');
            $table->double('fat',5,2);
            $table->double('fat_saturated',5,2);
            $table->double('carbohydrates',5,2);
            $table->double('carbohydrates_sugar',5,2);
            $table->double('protein',5,2);
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
        Schema::dropIfExists('nutrition');
    }
}