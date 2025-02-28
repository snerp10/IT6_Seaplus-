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
        Schema::create('pricing', function (Blueprint $table) {
            $table->id('price_id');
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('cascade'); // FK to products
            $table->decimal('original_price', 10, 2); // Cost price of the product
            $table->decimal('selling_price', 10, 2); // Selling price
            $table->decimal('markup', 10, 2); // Markup (selling price - original price)
            $table->date('start_date'); // When the price starts
            $table->date('end_date')->nullable(); // Optional, if price expires
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing');
    }
};
