<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVisitorOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitor_orders', function ($table) {
            $table->bigInteger('voucher_id')->unsigned()->index()->nullable()->after('user_id');
            $table->foreign('voucher_id')->references('id')->on('user_vouchers')->onDelete('cascade');
            $table->string('payment_reference_id')->nullable()->after('payment_status');
            $table->string('payment_external_id')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
