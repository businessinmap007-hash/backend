<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('menu_carts', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('business_id')->nullable();
        $table->unsignedBigInteger('menu_item_id');
        $table->unsignedBigInteger('size_id')->nullable();

        $table->integer('qty')->default(1);
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->decimal('total_price', 10, 2)->default(0);

        $table->text('options')->nullable();
        $table->text('notes')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_carts');
    }
};
