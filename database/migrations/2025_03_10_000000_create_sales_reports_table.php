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
            $table->string('name')->nullable(); // Adding name field
            $table->text('description')->nullable(); // Adding description field
            $table->date('date_from')->nullable(); // Adding date_from field
            $table->date('date_to')->nullable(); // Adding date_to field
            $table->timestamp('date_generated')->useCurrent();
            $table->decimal('total_sales', 15, 2)->nullable();
            $table->decimal('total_expenses', 15, 2)->nullable();
            $table->decimal('net_profit', 15, 2)->nullable();
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->foreignId('generated_by')->nullable()->constrained('employees', 'emp_id')->onDelete('cascade');
            $table->json('parameters')->nullable(); // Adding parameters field
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
