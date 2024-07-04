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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('note');
            $table->decimal('total_amount', 8, 2);
            $table->enum('status', ['Đang chờ xử lý', 'Đã xác nhận', 'Đang vận chuyển', 'Đã giao hàng', 'Đã hủy', 'Đang chờ thanh toán'])->default('Đang chờ xử lý');
            $table->timestamp('orderdate')->useCurrent();
            $table->string('shipping_address', 255)->nullable();
            $table->string('payment_method', 50);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
