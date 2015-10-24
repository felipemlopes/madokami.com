<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 24/10/2015
 * Time: 03:26
 */

namespace Madokami\Models;


use Illuminate\Database\Eloquent\Model;

class Scan extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'file_record_id', 'virustotal_scan_id', 'total', 'positives', 'scanned_at', 'scans' ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'scanned_at' ];

    /**
     * Get the scans attribute
     *
     * @param  string  $value
     * @return string
     */
    public function getScansAttribute($value) {
        return json_decode(gzdecode($value));
    }

    /**
     * Set the scans attribute
     *
     * @param  string  $value
     * @return string
     */
    public function setScansAttribute($value) {
        $this->attributes['scans'] = gzencode(json_encode($value));
    }

}