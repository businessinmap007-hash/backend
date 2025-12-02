<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('carts', function (Blueprint $table) {
      
        $table->dropColumn('ip_address');
    });
}

public function down()
{
    Schema::table('carts', function (Blueprint $table) {
        $table->string('ip_address')->nullable();
        $table->dropColumn('user_id');
    });
}


};
