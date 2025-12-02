<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('menu_order_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('menu_order_id'); // الطلب الرئيسي
            $table->unsignedBigInteger('menu_item_id');  // الصنف
            $table->integer('qty')->default(1);
            $table->string('size')->nullable();          // small / medium / large

            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);

            $table->timestamps();

            $table->foreign('menu_order_id')->references('id')->on('menu_orders')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_order_items');
    }
}
