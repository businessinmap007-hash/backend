<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('menu_orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');       // العميل
            $table->unsignedBigInteger('business_id');   // التاجر / المطعم
            $table->unsignedBigInteger('cart_id')->nullable();

            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('payment_method');            // cash - visa - wallet …
            $table->string('address');                   // العنوان النهائي
            $table->text('notes')->nullable();

            // pending / accepted / preparing / delivering / completed / cancelled
            $table->string('status')->default('pending');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_orders');
    }
}
