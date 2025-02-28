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
            $table->decimal('change_amount', 10, 2)->default(0.00);
            $table->decimal('outstanding_balance', 10, 2)->nullable();
            
            $table->timestamp('pay_date')->nullable();
            $table->enum('pay_method', ['Cash', 'Cash on Delivery', 'GCash']);
            $table->string('reference_number')->nullable(); // Para sa GCash transactions lang
            $table->string('invoice_number')->unique();
        
            $table->enum('pay_status', ['Paid', 'Partially Paid', 'Pending'])->default('Pending');
        
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
