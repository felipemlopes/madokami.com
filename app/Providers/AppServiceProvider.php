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
        FileRecord::deleting(function($fileRecord) {
            $fileRecord->deleteFile();
        });
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
}
