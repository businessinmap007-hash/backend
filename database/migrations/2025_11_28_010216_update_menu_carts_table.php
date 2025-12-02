<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMenuCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_carts', function (Blueprint $table) {

            // البزنس صاحب المنيو (مطعم)
            if (!Schema::hasColumn('menu_carts', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('user_id');
            }

            // العنصر الأساسي في المنيو
            if (!Schema::hasColumn('menu_carts', 'menu_item_id')) {
                $table->unsignedBigInteger('menu_item_id')->nullable()->after('business_id');
            }

            // الحجم (Small, Medium, Large)
            if (!Schema::hasColumn('menu_carts', 'size_id')) {
                $table->unsignedBigInteger('size_id')->nullable()->after('menu_item_id');
            }

            // الكمية
            if (!Schema::hasColumn('menu_carts', 'qty')) {
                $table->integer('qty')->default(1)->after('size_id');
            }

            // سعر الوحدة
            if (!Schema::hasColumn('menu_carts', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0)->after('qty');
            }

            // السعر الكلي
            if (!Schema::hasColumn('menu_carts', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('unit_price');
            }

            // إضافات (JSON)
            if (!Schema::hasColumn('menu_carts', 'options')) {
                $table->text('options')->nullable()->after('total_price');
            }

            // ملاحظات
            if (!Schema::hasColumn('menu_carts', 'notes')) {
                $table->text('notes')->nullable()->after('options');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_carts', function (Blueprint $table) {
            $table->dropColumn([
                'business_id',
                'menu_item_id',
                'size_id',
                'qty',
                'unit_price',
                'total_price',
                'options',
                'notes'
            ]);
        });
    }
}
