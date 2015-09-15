<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFileRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_records', function(Blueprint $table) {
            $table->increments('id');
            $table->string('client_name');
            $table->string('generated_name')->unique();
            $table->unsignedInteger('filesize');
            $table->char('hash', 64);
            $table->string('uploaded_by_ip');
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
        Schema::drop('file_records');
    }
}
