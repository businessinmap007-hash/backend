<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemExtrasTable extends Migration
{
    public function up()
    {
        Schema::create('menu_item_extras', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('menu_item_id');

            // اسم الإضافة
            $table->string('name_ar');
            $table->string('name_en')->nullable();

            // سعر الإضافة
            $table->decimal('price', 10, 2)->default(0);

            // متاحة أو لا
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_item_extras');
    }
}
