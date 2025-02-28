<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('prod_id');
            $table->string('name');
            $table->string('category');
            $table->string('unit');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->foreignId('supp_id')->constrained('suppliers', 'supp_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
