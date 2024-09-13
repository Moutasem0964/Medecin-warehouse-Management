<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('order_drug_warehouse', function (Blueprint $table) {
        $table->integer('amount')->after('warehouse_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('order_drug_warehouse', function (Blueprint $table) {
        $table->dropColumn('amount');
    });
}

};
