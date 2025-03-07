<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToSalesReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_reports', 'name')) {
                $table->string('name')->nullable();
            }
            if (!Schema::hasColumn('sales_reports', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('sales_reports', 'date_from')) {
                $table->date('date_from')->nullable();
            }
            if (!Schema::hasColumn('sales_reports', 'date_to')) {
                $table->date('date_to')->nullable();
            }
            if (!Schema::hasColumn('sales_reports', 'parameters')) {
                $table->json('parameters')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_reports', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'date_from', 'date_to', 'parameters']);
        });
    }
}
