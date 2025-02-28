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
            $table->foreignId('cus_id')->constrained('customers', 'cus_id')->onDelete('cascade');
            $table->dateTime('order_date');
            $table->decimal('total_amount', 10, 2);
            $table->enum('pay_status', ['Paid', 'Unpaid']);
            $table->enum('order_type', ['Online', 'Walk-in']);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
