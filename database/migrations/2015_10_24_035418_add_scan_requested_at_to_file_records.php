<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScanRequestedAtToFileRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_records', function(Blueprint $table) {
            $table->timestamp('scan_requested_at')->nullable()->after('uploaded_by_ip');
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
            $table->dropColumn('scan_requested_at');
        });
    }
}
