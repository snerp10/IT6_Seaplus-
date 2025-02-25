<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['Customer', 'Admin', 'Employee']);
            $table->foreignId('cus_id')->constrained('customers', 'cus_id')->onDelete('cascade')->nullable();
            $table->foreignId('emp_id')->constrained('employees', 'emp_id')->onDelete('cascade')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
