<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('supp_id');
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('contact_number', 15); // Standardized length
            $table->string('street');
            $table->string('city');
            $table->string('province');
            $table->text('prod_type'); // Para mas maraming product types
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
        Schema::dropIfExists('suppliers');
    }
}
