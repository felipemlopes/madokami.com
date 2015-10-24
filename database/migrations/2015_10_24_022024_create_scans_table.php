<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scans', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('file_record_id')->nullable();
            $table->foreign('file_record_id')->references('id')->on('file_records');
            $table->string('virustotal_scan_id');
            $table->integer('total');
            $table->integer('positives');
            $table->timestamp('scanned_at');
            $table->binary('scans');
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
        Schema::drop('scans');
    }
}
