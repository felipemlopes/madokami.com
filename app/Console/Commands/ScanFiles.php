<?php

namespace Madokami\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Madokami\Models\FileRecord;
use Madokami\Models\Scan;
use Madokami\VirusTotal\ApiThrottler;
use Madokami\VirusTotal\File as VirusTotalFile;
use VirusTotal\Exceptions\RateLimitException;

class ScanFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan uploads for infected files.';

    protected $throttler;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ApiThrottler $throttler)
    {
        parent::__construct();

        $this->throttler = $throttler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->tryExclusiveLock()) {
            FileRecord::whereRaw('RIGHT(client_name, 4) NOT IN (".png", ".jpg", "jpeg", "webm", ".gif")')
                ->chunk(100, function ($files) {
                foreach ($files as $file) {
                    if(empty($file->hash)) {
                        continue;
                    }

                    try {
                        $this->scanFile($file);
                    }
                    catch(RateLimitException $exception) {
                        $this->error('Rate limit hit');
                        sleep(60);
                    }
                    catch(\Exception $exception) {
                        $this->error($exception);
                    }
                }
            });
        }
    }

    protected function tryExclusiveLock() {
        $lockFile = storage_path('app/scan_files_lock');

        if(!file_exists($lockFile)) {
            touch($lockFile);
        }

        $fh = fopen($lockFile, 'r+');

        if(!flock($fh, LOCK_EX | LOCK_NB)) {
            $this->error('Failed to obtain exclusive lock');
            return false;
        }

        register_shutdown_function(function($fh) {
            fclose($fh);
        }, $fh, $lockFile);

        return true;
    }

    protected function scanFile(FileRecord $file) {
        $virusTotal = new VirusTotalFile(config('virustotal.api_key'));

        if($file->shouldCheckScan()) {
            $this->info(sprintf('Checking scan for file: %s (#%d)', $file->client_name, $file->id));

            $this->throttler->throttle(1);
            $result = $virusTotal->getReport($file->hash);

            $file->scan_checked_at = Carbon::now();
            $file->save();

            if ($result['response_code'] === 1) {
                $scannedAt = new Carbon($result['scan_date']);
                $cutoff = new Carbon('-2 weeks');

                // Check if this scan record already exists in DB
                $exists = (Scan::where('virustotal_scan_id', '=', $result['scan_id'])->count() > 0);

                if ($scannedAt->gte($cutoff) && !$exists) {
                    /** @var Scan $scan */
                    $scan = Scan::create([
                        'file_record_id' => $file->id,
                        'virustotal_scan_id' => $result['scan_id'],
                        'total' => $result['total'],
                        'positives' => $result['positives'],
                        'scanned_at' => $scannedAt,
                        'scans' => $result['scans'],
                    ]);

                    $this->info(sprintf('Scan result: %d/%d', $scan->positives, $scan->total));

                    // We now have a report from a requested scan
                    $file->scan_requested_at = null;
                    $file->save();

                    $deleted = $this->actionScanResult($file, $scan);

                    if($deleted) {
                        // Nothing else to do if deleted
                        return;
                    }
                }
            }
        }

        if($file->shouldScanFile()) {
            // Max filesize for public API is 32mb
            if($file->filesize <= (32 * pow(1024, 2))) {
                $this->info(sprintf('Uploading file for scan: %s (#%d)', $file->client_name, $file->id));

                $this->throttler->throttle(1);
                $result = $virusTotal->scan($file->filePath());

                if ($result['response_code'] === 1) {
                    $file->scan_requested_at = Carbon::now();
                    $file->save();
                }
            }
            else {
                // TODO: private API
            }
        }
        elseif($file->shouldRescanFile()) {
            $this->info(sprintf('Requesting re-scan for file: %s (#%d)', $file->client_name, $file->id));

            $this->throttler->throttle(1);
            $result = $virusTotal->rescan($file->hash);

            if ($result['response_code'] === 1) {
                $file->scan_requested_at = Carbon::now();
                $file->save();
            }
        }
    }

    protected function actionScanResult(FileRecord $file, Scan $scan) {
        $detectionRatio = $scan->positives / $scan->total;

        if($detectionRatio >= config('virustotal.detection_threshold')) {
            $this->info(sprintf('Deleting file: %s (#%d)', $file->client_name, $file->id));
            return $file->delete();
        }
        else {
            return false;
        }
    }
}
