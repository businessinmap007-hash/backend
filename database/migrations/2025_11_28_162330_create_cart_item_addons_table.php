<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('cart_item_addons', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('cart_item_id');
        $table->unsignedBigInteger('addon_id');
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_addons');
    }
};
