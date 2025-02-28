<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('emp_id');
            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->date('birthdate');
            $table->string('contact_number', 15);
            $table->string('email')->unique();
            
            // Address Breakdown
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10)->nullable();
            $table->string('country')->default('Philippines');
        
            // Employment Details
            $table->enum('position', ['Manager', 'Cashier', 'Driver', 'Laborer', 'Admin'])->default('Laborer'); 
            $table->decimal('salary', 10, 2)->default(0.00);
            $table->date('hired_date')->nullable();
            $table->enum('status', ['Active', 'Resigned', 'Terminated'])->default('Active');
        
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
