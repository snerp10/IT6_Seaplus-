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
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id('sup_prod_id');
            $table->foreignId('supp_id')->constrained('suppliers', 'supp_id')->onDelete('cascade');
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('cascade');
            $table->integer('min_order_qty')->default(1); // Minimum order quantity (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
