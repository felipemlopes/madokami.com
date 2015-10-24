<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 16/09/15
 * Time: 00:08
 */

namespace Madokami\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Madokami\Filters\FilterableInterface;
use Madokami\Filters\Filters;

/**
 * Class FileRecord
 *
 * The physical file this model represents is automatically moved to the "deleted" folder when the model is deleted.
 * @see \Madokami\Providers\AppServiceProvider::fileRecordDeletingListener()
 *
 * @package Madokami\Models
 */
class FileRecord extends Model {

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'client_name', 'generated_name', 'filesize', 'hash', 'uploaded_by_ip' ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'scan_requested_at', 'scan_checked_at' ];

    public function scans() {
        return $this->hasMany(Scan::class);
    }

    /**
     * Scope query via filters
     *
     * @param $query
     * @param Filters $filters
     * @return mixed
     */
    public function scopeFilter($query, Filters $filters) {
        foreach($filters as $field => $value) {
            if($field === 'search') {
                $query->search($value);
            }
            elseif($field === 'ip') {
                $query->ip($value);
            }
        }

        return $query;
    }

    public function scopeSearch($query, $search) {
        return $query->whereNested(function($nested) use($search) {
            $nested->where('client_name', 'LIKE', '%'.$search.'%')
                ->orWhere('generated_name', 'LIKE', '%'.$search.'%');
        });
    }

    public function scopeIp($query, $ip) {
        return $query->where('uploaded_by_ip', '=', $ip);
    }

    public function url() {
        return url($this->generated_name);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray() {
        $result = parent::toArray();

        $result['url'] = $this->url();

        return $result;
    }

    public function deleteFile() {
        $source = config('upload.directory').'/'.$this->generated_name;
        if(file_exists($source)) {
            $target = config('upload.deleted_directory').'/'.$this->generated_name;
            rename($source, $target);
        }
    }

    public function filePath() {
        if($this->trashed()) {
            return config('upload.deleted_directory').'/'.$this->generated_name;
        }
        else {
            return config('upload.directory').'/'.$this->generated_name;
        }
    }

    public function shouldScanFile() {
        if($this->scans()->count() > 0) {
            // Don't scan if we already have a scan record
            return false;
        }
        else {
            // Scan if this file has not already been submitted
            return ($this->scan_requested_at === null);
        }
    }

    public function shouldCheckScan() {
        if($this->scans()->count() > 0) {
            // Don't check if we already have a scan record
            return false;
        }
        elseif($this->scan_checked_at === null) {
            // Check if never been checked before
            return true;
        }
        else {
            if($this->scan_requested_at === null) {
                // If we haven't uploaded the file to VT then only check for fresh reports every 2 weeks
                $cutoff = new Carbon('-2 weeks');
            }
            else {
                // If we have uploaded a file then check every hour until we get a report
                $cutoff = new Carbon('-1 hour');
            }

            return $this->scan_checked_at->lt($cutoff);
        }
    }

    public function shouldRescanFile() {
        if($this->scans()->count() === 0) {
            // Can't re-scan if it's never been scanned before
            return false;
        }
        elseif($this->scan_requested_at !== null) {
            // Re-scan already requested
            return false;
        }
        else {
            // Re-scan if the latest scan for this file is older than 2 weeks old
            $lastScan = $this->scans()->orderBy('scanned_at', 'desc')->first();

            $cutoff = new Carbon('-2 weeks');

            return $lastScan->scanned_at->lt($cutoff);
        }
    }

}