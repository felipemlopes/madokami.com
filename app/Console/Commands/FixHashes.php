<?php

namespace Madokami\Console\Commands;

use Illuminate\Console\Command;
use Madokami\Models\FileRecord;

class FixHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:fix-hashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing hashes for file records.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = FileRecord::withTrashed()->where('hash', '=', '')->get();

        /** @var FileRecord $file */
        foreach($files as $index => $file) {
            if($file->fileExists()) {
                $fileHash = hash_file('sha256', $file->filePath());
                $file->hash = $fileHash;
                $file->save();
            }
            else {
                $this->output->error('File doesn\'t exist: '.$file->filePath());
            }

            $this->output->write(sprintf("\r%d/%d", ($index +1), $files->count()));
        }

        $this->output->success('Done');
    }
}
