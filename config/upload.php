<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 17/09/15
 * Time: 21:37
 */

return [

    /*
     * Target directory for uploads
     */
    'directory' => storage_path('uploads'),

    /*
     * Directory for deleted uploads
     */
    'deleted_directory' => storage_path('deleted'),

    /*
     * Max upload filesize in bytes
     */
    'max_size' => (256 * pow(1024, 2)),

];