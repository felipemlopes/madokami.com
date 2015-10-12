<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bans', function(Blueprint $table) {
            $table->increments('id');
            $table->string('ip');
            $table->index('ip');
            $table->unsignedInteger('file_record_id')->nullable();
            $table->foreign('file_record_id')->references('id')->on('file_records');
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
        Schema::drop('bans');
    }
}
