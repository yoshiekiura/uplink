<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->index()->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('category_id')->index()->unsigned();
            $table->foreign('category_id')->references('id')->on('user_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('cover');
            $table->string('platform');
            $table->string('platform_url', 355);
            $table->datetime('date');
            $table->integer('duration');
            $table->bigInteger('price');
            $table->bigInteger('price_sale')->nullable();
            $table->integer('quantity');
            $table->longText('custom_message');
            $table->string('action_button_text');
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
        Schema::dropIfExists('events');
    }
}
