<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('pay_id');
            $table->foreignId('cus_id')->constrained('customers', 'cus_id')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('pay_date');
            $table->enum('pay_method', ['Cash on Delivery', 'GCash', 'Cash']);
            $table->decimal('outstanding_balance', 10, 2);
            $table->string('invoice_number')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
