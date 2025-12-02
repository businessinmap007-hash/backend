<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');         // العميل
            $table->unsignedBigInteger('business_id');     // المطعم

            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('payment_method')->default('cash'); // cash – online – wallet

            $table->enum('status', [
                'pending',
                'accepted',
                'preparing',
                'delivering',
                'delivered',
                'canceled'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
