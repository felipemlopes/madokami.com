<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 16/09/15
 * Time: 00:08
 */

namespace Madokami\Models;

use Illuminate\Database\Eloquent\Model;

class FileRecord extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'client_name', 'generated_name', 'filesize', 'hash', 'uploaded_by_ip' ];

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

}