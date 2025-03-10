<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpIdToDeliveriesTable extends Migration
{
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->unsignedBigInteger('emp_id')->nullable()->after('delivery_id');
            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->dropColumn('emp_id');
        });
    }
}
