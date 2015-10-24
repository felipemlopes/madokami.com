<?php

namespace Madokami\Providers;

use Illuminate\Support\ServiceProvider;
use Madokami\Models\FileRecord;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->fileRecordDeletingListener();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function fileRecordDeletingListener() {
        FileRecord::deleting(function($fileRecord) {
            $fileRecord->deleteFile();
        });
    }
}
