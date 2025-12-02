<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemSizesTable extends Migration
{
    public function up()
    {
        Schema::create('menu_item_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('menu_item_id');

            // اسم الحجم (صغير - وسط - كبير)
            $table->string('name_ar');
            $table->string('name_en')->nullable();

            // السعر لهذا الحجم
            $table->decimal('price', 10, 2);

            // متاح / غير متاح
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
        Schema::dropIfExists('menu_item_sizes');
    }
}
