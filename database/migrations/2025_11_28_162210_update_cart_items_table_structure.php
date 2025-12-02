<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('cart_items', function (Blueprint $table) {

        // cart_id
        if (!Schema::hasColumn('cart_items', 'cart_id')) {
            $table->unsignedBigInteger('cart_id')->nullable()->after('id');
        }

        // menu_item_id
        if (!Schema::hasColumn('cart_items', 'menu_item_id')) {
            $table->unsignedBigInteger('menu_item_id')->nullable()->after('cart_id');
        }

        // size
        if (!Schema::hasColumn('cart_items', 'size')) {
            $table->string('size')->nullable()->after('qty');
        }
    });

    // نسخ البيانات من product_id إلى menu_item_id
    if (Schema::hasColumn('cart_items', 'product_id')) {
        DB::statement("UPDATE cart_items SET menu_item_id = product_id");
    }

    // -----------------------------------------
    // حذف الـ FOREIGN KEY قبل حذف العمود
    // -----------------------------------------
    Schema::table('cart_items', function (Blueprint $table) {

        // احذف FK لو موجود
        if (Schema::hasColumn('cart_items', 'product_id')) {
            try {
                $table->dropForeign('carts_product_id_foreign');
            } catch (\Exception $e) {
                // تجاهل الخطأ لو مش موجود
            }

            // الآن نحذف العمود
            $table->dropColumn('product_id');
        }
    });
}


};
