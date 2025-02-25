<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('inv_id');
            $table->foreignId('prod_id')->constrained('products', 'prod_id')->onDelete('cascade');
            $table->integer('curr_stock');
            $table->enum('move_type', ['Stock_in', 'Stock_out']);
            $table->integer('quantity');
            $table->date('move_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
}
