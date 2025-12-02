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
    Schema::create('menu_cart', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('menu_item_id');   // عنصر منيو
        $table->unsignedBigInteger('business_id');    // المطعم أو البزنس
        $table->integer('qty')->default(1);

        $table->string('size')->nullable();   // Small - Medium - Large
        $table->json('addons')->nullable();   // إضافات
        $table->json('options')->nullable();  // اختيارات
        $table->text('notes')->nullable();

        $table->decimal('price_unit', 10, 2)->default(0);   // سعر العنصر بدون إضافات
        $table->decimal('price_addons', 10, 2)->default(0); // سعر الإضافات
        $table->decimal('price_total', 10, 2)->default(0);  // السعر النهائي

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_cart');
    }
};
