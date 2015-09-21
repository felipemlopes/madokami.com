<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 21/09/2015
 * Time: 16:51
 */

namespace Madokami\Formatters;


class FileSizeFormatter {

    public static function format($input) {
        if($input === 0) {
            return '0';
        }

        $base = log($input) / log(1024);
        $suffixes = array('', 'K', 'M', 'G', 'T');
        $suffix = $suffixes[floor($base)].'iB';
        return number_format(pow(1024, $base - floor($base)), 0) . $suffix;
    }

}