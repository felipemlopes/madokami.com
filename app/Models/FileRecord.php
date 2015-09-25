<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 16/09/15
 * Time: 00:08
 */

namespace Madokami\Models;

use Illuminate\Database\Eloquent\Model;
use Madokami\Filters\FilterableInterface;
use Madokami\Filters\Filters;

class FileRecord extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'client_name', 'generated_name', 'filesize', 'hash', 'uploaded_by_ip' ];

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
        return $query->where('client_name', 'like', '%'.$search.'%');
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

}