<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScanCheckedAtToFileRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_records', function(Blueprint $table) {
            $table->timestamp('scan_checked_at')->nullable()->after('scan_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('file_records', function(Blueprint $table) {
            $table->dropColumn('scan_checked_at');
        });
    }
}
