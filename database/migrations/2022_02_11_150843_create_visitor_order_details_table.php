<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_order_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('visitor_orders')->onDelete('cascade');
            $table->string('product_type');

            $table->bigInteger('event')->unsigned()->index()->nullable();
            $table->foreign('event')->references('id')->on('events')->onDelete('cascade');
            $table->bigInteger('digital_product')->unsigned()->index()->nullable();
            $table->foreign('digital_product')->references('id')->on('digital_products')->onDelete('cascade');
            $table->bigInteger('physical_product')->unsigned()->index()->nullable();
            $table->foreign('physical_product')->references('id')->on('physical_products')->onDelete('cascade');
            $table->bigInteger('support')->unsigned()->index()->nullable();
            $table->foreign('support')->references('id')->on('supports')->onDelete('cascade');
            $table->bigInteger('chat_subscription')->unsigned()->index()->nullable();
            $table->foreign('chat_subscription')->references('id')->on('chat_subscriptions')->onDelete('cascade');

            $table->integer('quantity');
            $table->bigInteger('total_price');
            
            $table->string('shipping_origin')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_waybill')->nullable();
            $table->string('shipping_courier')->nullable();
            $table->integer('shipping_weight')->nullable();
            $table->integer('shipping_cost')->nullable();

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
        Schema::dropIfExists('visitor_order_details');
    }
}
