<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('cus_id');
            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('birthdate');
            $table->string('contact_number', 15);
            $table->text('email')->unique();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable(); // Useful for PH locations
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10)->nullable();
            $table->string('country')->default('Philippines'); // Default to PH
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
