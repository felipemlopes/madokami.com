<?php

namespace Madokami\Console\Commands;

use Illuminate\Console\Command;
use Madokami\Models\FileRecord;
use Madokami\VirusTotal\File as VirusTotalFile;

class ScanFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan uploads for infected files.';

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
        $file = FileRecord::findOrFail(39);

        $virusTotal = new VirusTotalFile(config('virustotal.api_key'));
        $result = $virusTotal->privateUploadUrl();
        $privateUploadUrl = $result['upload_url'];

        $this->output->writeln($privateUploadUrl);

        $this->output->writeln($file->filePath());

        $privateUploadUrl = str_replace('https:', 'http:', $privateUploadUrl);

        $result = $virusTotal->privateScan($privateUploadUrl, $file->filePath());

        dd($result);

    }
}
