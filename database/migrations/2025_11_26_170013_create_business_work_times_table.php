<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_work_times', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('business_id');
        $table->integer('day_of_week'); // 0 = الأحد .. 6 = السبت
        $table->time('open_time');
        $table->time('close_time');
        $table->timestamps();

        $table->foreign('business_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_work_times');
    }
};
