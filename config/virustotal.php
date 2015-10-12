<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 12/10/2015
 * Time: 23:06
 */

return [

    /*
     * Use VirusTotal API to detect infected files on upload
     */
    'enabled' => true,

    /*
     * VirusTotal API key
     * See for details: https://www.virustotal.com/en/documentation/public-api/#getting-started
     */
    'api_key' => env('VIRUSTOTAL_API_KEY'),

    /*
     * Percentage of detections at which the file is considered infected
     */
    'detection_threshold' => 0.2,

];