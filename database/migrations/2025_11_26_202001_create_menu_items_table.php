<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuItemsTable extends Migration
{
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            // صاحب المنيو (المطعم / البزنس)
            $table->unsignedBigInteger('business_id');

            // اسم الصنف
            $table->string('name_ar');
            $table->string('name_en')->nullable();

            // وصف
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            // صورة أساسية
            $table->string('image')->nullable();

            // هل الصنف متاح حالياً؟
            $table->boolean('is_active')->default(true);

            // ترتيب في قائمة المنيو
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // FK
            $table->foreign('business_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
