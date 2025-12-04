<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            // أطراف العملية
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('business_id');

            // ارتباط بالحجز / طلب / رحلة / مناقصة إلخ
            $table->string('target_type'); // booking, order, ride, tender ...
            $table->unsignedBigInteger('target_id');

            // القيمة الكلية للعملية
            $table->decimal('total_amount', 10, 2);

            // نسب الدفعة المقدمة (لازم <= 20)
            $table->unsignedTinyInteger('client_percent')->default(0);
            $table->unsignedTinyInteger('business_percent')->default(0);

            // مبالغ الدفعة المقدمة المحسوبة
            $table->decimal('client_amount', 10, 2)->default(0);
            $table->decimal('business_amount', 10, 2)->default(0);

            // الحالة
            $table->enum('status', ['frozen', 'released', 'refunded', 'dispute'])->default('frozen');

            // تأكيدات
            $table->boolean('client_confirmed')->default(false);          // تأكيد إتمام داخل BIM
            $table->boolean('business_confirmed')->default(false);

            $table->boolean('client_outside_bim')->default(false);        // دفع خارج BIM
            $table->boolean('business_outside_bim')->default(false);

            // تواريخ مهمة
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            $table->timestamps();

            // فهارس وعلاقات (اختياري حسب مشروعك)
            $table->index(['target_type', 'target_id']);
            $table->index(['client_id']);
            $table->index(['business_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
