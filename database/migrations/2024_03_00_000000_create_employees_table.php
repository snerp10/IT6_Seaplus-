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
            $table->string('mname');
            $table->string('lname');
            $table->date('birthdate');
            $table->string('contact_number');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('position');
            $table->decimal('salary', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
