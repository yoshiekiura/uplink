<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_stats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('link_id')->index()->unsigned();
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->bigInteger('count');
            $table->date('date');
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
        Schema::dropIfExists('link_stats');
    }
}
