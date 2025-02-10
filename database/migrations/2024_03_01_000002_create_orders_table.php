<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            $table->dateTime('order_date');
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method');
            $table->string('payment_status');
            $table->string('order_type');
            $table->string('delivery_status');
            $table->text('delivery_address')->nullable();
            $table->string('delivery_schedule')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
