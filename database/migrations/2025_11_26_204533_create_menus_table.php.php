<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('business_id'); // صاحب المطعم
        $table->string('name_ar');
        $table->string('name_en');
        $table->text('description')->nullable();
        $table->decimal('base_price', 10, 2);
        $table->string('image')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();

        $table->foreign('business_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
