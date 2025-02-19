<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->date('date_generated');
            $table->decimal('total_sales', 15, 2);
            $table->decimal('total_expenses', 15, 2);
            $table->decimal('net_profit', 15, 2);
            $table->enum('report_type', ['daily', 'weekly', 'monthly']);
            $table->foreignId('generated_by')->constrained('users', 'user_id')->onDelete('cascade');
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
        Schema::dropIfExists('sales_reports');
    }
}
